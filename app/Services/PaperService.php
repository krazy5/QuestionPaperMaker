<?php

namespace App\Services;

use App\Exceptions\MarksLimitExceededException;
use App\Models\AcademicClassModel;
use App\Models\Paper;
use App\Models\PaperBlueprint;
use App\Models\Question;
use App\Models\SectionRule;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaperService
{
    /**
     * Create a paper from the provided blueprint and auto-fill it with questions.
     */
    public function createPaperFromBlueprint(PaperBlueprint $blueprint, $examDate, int $instituteId, ?string $timeAllowed = null): Paper
    {
        $blueprint->loadMissing('sections.rules');

        $paper = Paper::create([
            'title'              => $blueprint->name,
            'paper_blueprint_id' => $blueprint->id,
            'institute_id'       => $instituteId,
            'board_id'           => $blueprint->board_id,
            'class_id'           => $blueprint->class_id,
            'subject_id'         => $blueprint->subject_id,
            'total_marks'        => $blueprint->total_marks,
            'time_allowed'       => $timeAllowed ?? '3 Hours',
            'exam_date'          => $this->normalizeExamDate($examDate),
        ]);

        $this->attachQuestionsFromBlueprint($paper, $blueprint);

        return $paper;
    }

    /**
     * Auto-fill an existing paper using its blueprint rules.
     */
    public function autoFillPaper(Paper $paper, array $chapterIds = [], bool $avoidDuplicatesAcrossPaper = true): void
    {
        $paper->loadMissing('blueprint.sections.rules');
        $blueprint = $paper->blueprint;

        if (!$blueprint) {
            return;
        }

        $selectedChapterIds = !empty($chapterIds)
            ? array_map('intval', $chapterIds)
            : ($blueprint->selected_chapters ?? []);

        DB::transaction(function () use ($paper, $selectedChapterIds, $avoidDuplicatesAcrossPaper) {
            $alreadyPickedIds = $avoidDuplicatesAcrossPaper
                ? $paper->questions()->pluck('questions.id')->all()
                : [];

            foreach ($paper->blueprint->sections as $section) {
                foreach ($section->rules as $rule) {
                    $paper->questions()->newPivotStatement()
                        ->where('paper_id', $paper->id)
                        ->where('section_rule_id', $rule->id)
                        ->delete();

                    $query = $this->candidateQueryForRule($paper, $rule, $selectedChapterIds);

                    if ($avoidDuplicatesAcrossPaper && !empty($alreadyPickedIds)) {
                        $query->whereNotIn('id', $alreadyPickedIds);
                    }

                    $need = (int) $rule->number_of_questions_to_select;
                    if ($need <= 0) {
                        continue;
                    }

                    $candidateIds = $query->inRandomOrder()
                        ->limit($need)
                        ->pluck('id')
                        ->all();

                    if (empty($candidateIds)) {
                        continue;
                    }

                    $attachPayload = [];
                    $sort = 1;
                    foreach ($candidateIds as $questionId) {
                        $attachPayload[$questionId] = [
                            'marks'           => (int) $rule->marks_per_question,
                            'section_rule_id' => $rule->id,
                            'sort_order'      => $sort++,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }

                    $paper->questions()->attach($attachPayload);

                    if ($avoidDuplicatesAcrossPaper) {
                        $alreadyPickedIds = array_merge($alreadyPickedIds, array_keys($attachPayload));
                    }
                }
            }
        });
    }

    /**
     * Return a candidate query for the given rule.
     */
    public function candidateQueryForRule(Paper $paper, SectionRule $rule, array $chapterIds = []): Builder
    {
        return Question::query()
            ->where('question_type', $rule->question_type)
            ->when($paper->board_id, fn (Builder $query) => $query->where('board_id', $paper->board_id))
            ->when($paper->class_id, fn (Builder $query) => $query->whereJsonContains('class_id', (int) $paper->class_id))
            ->when($paper->subject_id, fn (Builder $query) => $query->whereJsonContains('subject_id', (int) $paper->subject_id))
            ->when(!empty($chapterIds), fn (Builder $query) => $query->whereIn('chapter_id', $chapterIds))
            ->where('marks', $rule->marks_per_question)
            ->where(fn (Builder $query) => $query
                ->where('approved', true)
                ->orWhere('institute_id', $paper->institute_id)
            );
    }

    /**
     * Find a blueprint that matches the given paper context.
     */
    public function findMatchingBlueprint(Paper $paper): ?PaperBlueprint
    {
        return PaperBlueprint::query()
            ->where('board_id', $paper->board_id)
            ->where('class_id', $paper->class_id)
            ->where('subject_id', $paper->subject_id)
            ->where('total_marks', $paper->total_marks)
            ->orderByRaw('CASE WHEN institute_id IS NULL THEN 0 ELSE 1 END')
            ->latest()
            ->first();
    }

    /**
     * Attach a question to the paper, handling both rule-based and free-form modes.
     *
     * @throws MarksLimitExceededException
     */
    public function attachQuestion(Paper $paper, Question $question, ?SectionRule $rule = null): int
    {
        if ($rule) {
            $existsForRule = $paper->questions()
                ->where('questions.id', $question->id)
                ->wherePivot('section_rule_id', $rule->id)
                ->exists();

            if (!$existsForRule) {
                $currentMarks = $this->currentSelectedMarks($paper);
                $marksToAdd = (int) $rule->marks_per_question;

                if (($currentMarks + $marksToAdd) > (int) $paper->total_marks) {
                    throw new MarksLimitExceededException($currentMarks, 'Adding this question exceeds the total marks limit.');
                }

                $paper->questions()->attach($question->id, [
                    'marks'           => $marksToAdd,
                    'section_rule_id' => $rule->id,
                    'sort_order'      => $paper->questions()->count() + 1,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            } else {
                $paper->questions()->updateExistingPivot($question->id, [
                    'marks'           => (int) $rule->marks_per_question,
                    'section_rule_id' => $rule->id,
                    'updated_at'      => now(),
                ]);
            }
        } else {
            $alreadyAttached = $paper->questions()
                ->where('questions.id', $question->id)
                ->exists();

            if (!$alreadyAttached) {
                $currentMarks = $this->currentSelectedMarks($paper);
                $marksToAdd = (int) $question->marks;

                if (($currentMarks + $marksToAdd) > (int) $paper->total_marks) {
                    throw new MarksLimitExceededException($currentMarks, 'Adding this question exceeds the total marks limit.');
                }

                $paper->questions()->attach($question->id, [
                    'marks'           => $marksToAdd,
                    'section_rule_id' => null,
                    'sort_order'      => $paper->questions()->count() + 1,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        return $this->currentSelectedMarks($paper);
    }

    /**
     * Detach a question from the paper and return the updated total marks.
     */
    public function detachQuestion(Paper $paper, Question $question, ?SectionRule $rule = null): int
    {
        $pivotQuery = $paper->questions()->newPivotStatement()
            ->where('paper_id', $paper->id)
            ->where('question_id', $question->id);

        if ($rule) {
            $pivotQuery->where('section_rule_id', $rule->id);
        } else {
            $pivotQuery->whereNull('section_rule_id');
        }

        $pivotQuery->delete();

        return $this->currentSelectedMarks($paper);
    }

    /**
     * Get the current sum of marks for questions attached to the paper.
     */
    public function currentSelectedMarks(Paper $paper): int
    {
        return (int) DB::table('paper_question')
            ->where('paper_id', $paper->id)
            ->sum('marks');
    }

    /**
     * Retrieve the classes available for the given board.
     */
    public function getClassesForBoard(int $boardId): Collection
    {
        return AcademicClassModel::query()
            ->where('board_id', $boardId)
            ->orderBy('name')   
            ->get(['id', 'name']);
    }

    /**
     * Retrieve the subjects available for the given class.
     */
    public function getSubjectsForClass(int $classId): Collection
    {
        return Subject::query()
            ->where('class_id', $classId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Attach questions for every rule in a blueprint to the paper.
     */
    protected function attachQuestionsFromBlueprint(Paper $paper, PaperBlueprint $blueprint): void
    {
        $usedQuestionIds = collect();
        $attachPayload = [];
        $selectedChapters = $blueprint->selected_chapters ?? [];

        foreach ($blueprint->sections as $section) {
            foreach ($section->rules as $rule) {
                $displayCount = (int) $rule->number_of_questions_to_select;
                if ($displayCount <= 0) {
                    continue;
                }

                $picked = $this->pickQuestionsForRule($paper, $rule, $selectedChapters, $usedQuestionIds, $displayCount);

                foreach ($picked as $question) {
                    $usedQuestionIds->push($question->id);
                    $attachPayload[$question->id] = [
                        'marks'           => (int) $rule->marks_per_question,
                        'section_rule_id' => $rule->id,
                    ];
                }
            }
        }

        if (!empty($attachPayload)) {
            $paper->questions()->sync($attachPayload);
        }
    }

    /**
     * Pick questions for a specific rule, falling back when there are insufficient chapter matches.
     */
    protected function pickQuestionsForRule(Paper $paper, SectionRule $rule, array $chapterIds, Collection $usedQuestionIds, int $displayCount): Collection
    {
        $primary = $this->candidateQueryForRule($paper, $rule, $chapterIds)
            ->whereNotIn('id', $usedQuestionIds)
            ->inRandomOrder()
            ->limit($displayCount)
            ->get();

        if ($primary->count() >= $displayCount || empty($chapterIds)) {
            return $primary;
        }

        $needed = $displayCount - $primary->count();
        $fallback = $this->candidateQueryForRule($paper, $rule)
            ->whereNotIn('id', $usedQuestionIds->merge($primary->pluck('id')))
            ->inRandomOrder()
            ->limit($needed)
            ->get();

        return $primary->concat($fallback);
    }

    /**
     * Convert the exam date into a standard Y-m-d string.
     */
    protected function normalizeExamDate($examDate): ?string
    {
        if ($examDate instanceof \DateTimeInterface) {
            return $examDate->format('Y-m-d');
        }

        if (is_string($examDate) && trim($examDate) !== '') {
            try {
                return Carbon::parse($examDate)->format('Y-m-d');
            } catch (\Throwable $throwable) {
                return null;
            }
        }

        return null;
    }
}
