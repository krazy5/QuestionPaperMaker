<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Paper;
use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Chapter;
use App\Models\PaperBlueprint;
use App\Models\SectionRule;
use Illuminate\Support\Facades\DB;
use App\Models\ManualSubscription; // 👈


class PaperController extends Controller
{
    /**
     * Display a listing of the institute's papers.
     */
    public function index()
    {
        $user = auth()->user();
        $activeSubscription = $user->activeManualSubscription();


        // ✅ CORRECTED THIS LINE
        $papers = Paper::where('institute_id', $user->id)
            ->with(['subject', 'academicClass'])
            ->latest()
            ->paginate(15);
            
        return view('institute.papers.index', compact('papers', 'activeSubscription'));
    }

    /**
     * Display the specified paper for preview.
     */
    public function preview(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) {
            abort(403);
        }
        // Eager load all necessary relationships for the view
        $paper->load('questions', 'blueprint.sections.rules', 'subject', 'academicClass');
        $blueprint = $paper->blueprint;
        return view('institute.papers.preview', compact('paper', 'blueprint'));
    }

    /**
     * Create a new paper from a blueprint and auto-fill it.
     */
    // In app/Http-Controllers/Institute/PaperController.php

        // In PaperController.php

            
        
        public function createFromBlueprint(Request $request, PaperBlueprint $blueprint)
        {
            if ($blueprint->institute_id !== auth()->id()) {
                abort(403);
            }

            $request->validate(['exam_date' => 'required|date']);
            $blueprint->load('sections.rules');

            $newPaper = Paper::create([
                'title'              => $blueprint->name,
                'paper_blueprint_id' => $blueprint->id,
                'institute_id'       => auth()->id(),
                'board_id'           => $blueprint->board_id,
                'class_id'           => $blueprint->class_id,
                'subject_id'         => $blueprint->subject_id,
                'total_marks'        => $blueprint->total_marks,
                'time_allowed'       => '3 Hours',
                'exam_date'          => $request->input('exam_date'),
            ]);

            $usedQuestionIds = collect(); // prevent duplicates across rules
            $attach = [];

            foreach ($blueprint->sections as $section) {
                foreach ($section->rules as $rule) {

                    $displayCount = (int) $rule->number_of_questions_to_select;


                    // Build query for this rule
                    $q = Question::query()
                        ->where('board_id', $newPaper->board_id)
                        ->whereJsonContains('class_id', $newPaper->class_id)   // class_id stored as JSON
                        ->whereJsonContains('subject_id', $newPaper->subject_id) // subject_id stored as JSON
                        ->where('question_type', $rule->question_type)
                        ->where('marks', $rule->marks_per_question)
                        ->where(fn ($sub) => $sub->where('approved', true)
                                                ->orWhere('institute_id', auth()->id()))
                        ->when(!empty($blueprint->selected_chapters), fn ($sub) =>
                            $sub->whereIn('chapter_id', $blueprint->selected_chapters)
                        )
                        ->whereNotIn('id', $usedQuestionIds) // avoid duplicates
                        ->inRandomOrder()
                        ->limit($displayCount);

                    $picked = $q->get();

                    // If not enough questions, relax chapter filter (fallback), then difficulty (optional)
                    if ($picked->count() < $displayCount && !empty($blueprint->selected_chapters)) {
                        $needed = $displayCount - $picked->count();
                        $fallback = Question::query()
                            ->where('board_id', $newPaper->board_id)
                            ->whereJsonContains('class_id', $newPaper->class_id)
                            ->whereJsonContains('subject_id', $newPaper->subject_id)
                            ->where('question_type', $rule->question_type)
                            ->where('marks', $rule->marks_per_question)
                            ->where(fn ($sub) => $sub->where('approved', true)
                                                    ->orWhere('institute_id', auth()->id()))
                            ->whereNotIn('id', $usedQuestionIds->merge($picked->pluck('id')))
                            ->inRandomOrder()
                            ->limit($needed)
                            ->get();
                        $picked = $picked->concat($fallback);
                    }

                    // Attach what we have
                    foreach ($picked as $question) {
                        $usedQuestionIds->push($question->id);
                        $attach[$question->id] = [
                            'marks'           => $question->marks,
                            'section_rule_id' => $rule->id,
                        ];
                    }
                }
            }

            if (!empty($attach)) {
                $newPaper->questions()->sync($attach);
            }

            return redirect()
                ->route('institute.papers.preview', $newPaper)
                ->with('success', 'Paper created and auto-filled successfully!');
        }
        
        
        
        // new method for autofilling ==============================================
       
        
        
        public function autoFillBlueprint(Paper $paper, Request $request)
        {
            // Load relations we need
            $paper->load('blueprint.sections.rules', 'questions');

            // Prefer chapters[] sent from the page's "Choose Chapters" modal; fallback to blueprint; else "all"
            $selectedChapterIds = $request->has('chapters')
                ? array_map('intval', (array) $request->input('chapters', []))
                : ($paper->blueprint?->selected_chapters ?? []);


            // If you want to avoid duplicates across the entire paper, keep this true
            $avoidDuplicatesAcrossPaper = true;

            DB::transaction(function () use ($paper, $selectedChapterIds, $avoidDuplicatesAcrossPaper) {
                $alreadyPickedIds = $avoidDuplicatesAcrossPaper
                    ? $paper->questions()->pluck('questions.id')->all()
                    : [];

                foreach ($paper->blueprint->sections as $section) {
                    foreach ($section->rules as $rule) {

                        // 1) Clear existing rows for THIS rule (so recounts are correct)
                        $paper->questions()->newPivotStatement()
                            ->where('paper_id', $paper->id)
                            ->where('section_rule_id', $rule->id)
                            ->delete();

                        // 2) Build candidate query for this rule
                        $q = $this->candidateQueryForRule($paper, $rule, $selectedChapterIds);

                        if ($avoidDuplicatesAcrossPaper && !empty($alreadyPickedIds)) {
                            $q->whereNotIn('id', $alreadyPickedIds);
                        }

                        $need = (int) $rule->number_of_questions_to_select;

                        $candidateIds = $q->inRandomOrder()
                            ->limit($need)
                            ->pluck('id')
                            ->all();

                        // 3) Attach with pivot attributes (marks, section_rule_id, sort_order)
                        $attachPayload = [];
                        $sort = 1;
                        foreach ($candidateIds as $qid) {
                            $attachPayload[$qid] = [
                                'marks'            => (int) $rule->marks_per_question,
                                'section_rule_id'  => $rule->id,
                                'sort_order'       => $sort++,
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ];
                        }

                        if (!empty($attachPayload)) {
                            $paper->questions()->attach($attachPayload);
                            if ($avoidDuplicatesAcrossPaper) {
                                $alreadyPickedIds = array_merge($alreadyPickedIds, array_keys($attachPayload));
                            }
                        }
                    }
                }

                // // 4) Optional: recompute total marks from pivot
                // $sum = DB::table('paper_question')
                //     ->where('paper_id', $paper->id)
                //     ->sum('marks');

                // $paper->total_marks = (int) $sum;
                // $paper->save();
            });

            return back()->with('status', 'Auto-filled successfully.');
        }






    // --- OLDER MANUAL WORKFLOW & HELPER METHODS ---

    public function create()
    {
        $boards = Board::all();
        $classes = AcademicClassModel::all();
        $subjects = [];
        return view('institute.papers.create', compact('boards', 'classes', 'subjects'));
    }
    
    public function store(Request $request)
        {
            $validatedData = $request->validate([
                'board_id'     => 'required|exists:boards,id',
                'class_id'     => 'required|exists:academic_class_models,id',
                'subject_id'   => 'required|exists:subjects,id',
                'title'        => 'required|string|max:255',
                'total_marks'  => 'required|integer|min:1',
                'time_allowed' => 'required|string|max:50',
                'exam_date'    => 'nullable|date',
                'instructions' => 'nullable|string',
            ]);

            $validatedData['institute_id'] = auth()->id();
            $paper = Paper::create($validatedData);

            // 🔎 Detect a matching ADMIN (or global) blueprint
            // If you mark admin templates with institute_id = null, this prefers those.
            $matchingBlueprint = PaperBlueprint::query()
                ->where('board_id',  $paper->board_id)
                ->where('class_id',  $paper->class_id)
                ->where('subject_id',$paper->subject_id)
                ->where('total_marks', $paper->total_marks)
                ->orderByRaw('CASE WHEN institute_id IS NULL THEN 0 ELSE 1 END') // prefer admin/global first
                ->latest()
                ->first();

            if ($matchingBlueprint) {
                // 👉 show decision page
                return redirect()->route('institute.papers.choose_blueprint', $paper)
                    ->with('detected_blueprint_id', $matchingBlueprint->id);
            }

            // No blueprint found → go free-form
            return redirect()->route('institute.papers.questions.select', $paper);
        }

        public function chooseBlueprint(Paper $paper)
        {
            if ($paper->institute_id !== auth()->id()) abort(403);

            $detectedId = session('detected_blueprint_id');
            if (!$detectedId) {
                // Nothing detected (or direct hit) → go to free-form
                return redirect()->route('institute.papers.questions.select', $paper);
            }

            $blueprint = PaperBlueprint::find($detectedId);
            if (!$blueprint) {
                return redirect()->route('institute.papers.questions.select', $paper);
            }

            // You might want to ensure it still matches (safety)
            if (
                $blueprint->board_id  != $paper->board_id  ||
                $blueprint->class_id  != $paper->class_id  ||
                $blueprint->subject_id!= $paper->subject_id||
                (int)$blueprint->total_marks !== (int)$paper->total_marks
            ) {
                return redirect()->route('institute.papers.questions.select', $paper);
            }

            return view('institute.papers.choose_blueprint', compact('paper', 'blueprint'));
        }

        public function adoptBlueprint(Paper $paper, PaperBlueprint $blueprint)
        {
            if ($paper->institute_id !== auth()->id()) abort(403);

            // Safety: ensure it matches the paper context
            if (
                $blueprint->board_id  != $paper->board_id  ||
                $blueprint->class_id  != $paper->class_id  ||
                $blueprint->subject_id!= $paper->subject_id||
                (int)$blueprint->total_marks !== (int)$paper->total_marks
            ) {
                return redirect()->route('institute.papers.questions.select', $paper)
                    ->with('error', 'Selected blueprint does not match this paper.');
            }

            $paper->update([
                'paper_blueprint_id' => $blueprint->id,
            ]);

            // Send them to the fulfill page (with Auto-fill button already present)
            return redirect()->route('institute.papers.fulfill_blueprint', $paper)
                ->with('success', 'Blueprint linked. You can now auto-fill or add questions per section.');
        }


        public function apiQuestionsForRule(Paper $paper, SectionRule $rule, Request $request)
        {
            // Filter by chapters[] from query string
            $chapterIds = array_filter((array) $request->query('chapters', []));

            $query = $this->candidateQueryForRule($paper, $rule, $chapterIds);

            $available = $query
                ->select('id', 'question_text')
                ->orderBy('id', 'desc')
                ->limit(300) // safety cap; adjust as needed
                ->get();

            $selectedIds = $paper->questions()
                ->wherePivot('section_rule_id', $rule->id)
                ->pluck('questions.id');

            return response()->json([
                'available_questions' => $available,
                'selected_ids'        => $selectedIds,
            ]);
        }


        // PaperController.php
        public function apiPaperRuleStats(Paper $paper)
        {
            $rows = DB::table('paper_question')
                ->select('section_rule_id as rule_id', DB::raw('COUNT(*) as cnt'))
                ->where('paper_id', $paper->id)
                ->groupBy('section_rule_id')
                ->get();

            return response()->json($rows);
        }


        protected function candidateQueryForRule(Paper $paper, SectionRule $rule, array $chapterIds = [])
        {
            $q = Question::query()
                ->where('question_type', $rule->question_type)
                // Board is a normal int column on questions
                ->when($paper->board_id, fn($qq) => $qq->where('board_id', $paper->board_id))
                // class_id & subject_id are JSON on Question; Paper holds ints
                ->when($paper->class_id, fn($qq) => $qq->whereJsonContains('class_id', (int) $paper->class_id))
                ->when($paper->subject_id, fn($qq) => $qq->whereJsonContains('subject_id', (int) $paper->subject_id))
                // Optional chapter restriction
                ->when(!empty($chapterIds), fn($qq) => $qq->whereIn('chapter_id', $chapterIds));

            return $q;
        }





        public function fulfillBlueprint(Paper $paper)
        {
            if ($paper->institute_id !== auth()->id()) {
                abort(403);
            }

            // Prefer the blueprint already linked to the paper
            $blueprint = $paper->blueprint;

            // Optional fallback: try to find a matching blueprint by context
            if (!$blueprint) {
                $blueprint = PaperBlueprint::query()
                    ->where('board_id',  $paper->board_id)
                    ->where('class_id',  $paper->class_id)
                    ->where('subject_id',$paper->subject_id)
                    ->where('total_marks', $paper->total_marks)
                    ->orderByRaw('CASE WHEN institute_id IS NULL THEN 0 ELSE 1 END')
                    ->latest()
                    ->first();

                // If still nothing, send them to free-form selection
                if (!$blueprint) {
                    return redirect()
                        ->route('institute.papers.questions.select', $paper)
                        ->with('error', 'No matching blueprint found for this paper.');
                }

                // (Optional) link it now so future calls work smoothly
                $paper->update(['paper_blueprint_id' => $blueprint->id]);
            }

            $blueprint->load('sections.rules');

            // Chapters list for the modal filter in fulfill view
            $chapters = Chapter::where('subject_id', $paper->subject_id)
                ->orderBy('name')
                ->get();

                $ruleCounts = DB::table('paper_question')
                    ->where('paper_id', $paper->id)
                    ->select('section_rule_id', DB::raw('COUNT(*) as cnt'))
                    ->groupBy('section_rule_id')
                    ->pluck('cnt', 'section_rule_id')
                    ->toArray();

            return view('institute.papers.fulfill_blueprint', compact('paper', 'blueprint', 'chapters','ruleCounts'));
        }

 


    public function selectQuestions(Request $request, Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        $chapters = Chapter::where('subject_id', $paper->subject_id)->get();
        $query = Question::where('board_id', $paper->board_id)
            ->whereJsonContains('class_id', $paper->class_id) 
            ->whereJsonContains('subject_id', $paper->subject_id)
            ->where(fn ($q) => $q->where('approved', true)->orWhere('institute_id', auth()->id()));

        if ($request->filled('chapter') && $request->chapter != 'all') {
            $query->where('chapter_id', $request->chapter);
        }
        if ($request->filled('types')) {
            $query->whereIn('question_type', $request->types);
        }
        $questions = $query->latest()->paginate(10)->withQueryString();
        // ✨ Load all existing questions once to be efficient
            $existingQuestions = $paper->questions()->get();

        return view('institute.papers.select_questions', [
            'paper' => $paper,
            'chapters' => $chapters,
            'questions' => $questions,
            'existingQuestionIds' => $paper->questions->pluck('id'),
            'currentChapter' => $request->chapter ?? 'all',
            'currentTypes' => $request->types ?? [],
            'currentMarks' => $paper->questions->sum(fn ($q) => (int) $q->pivot->marks),
            'selectedQuestionCount' => $existingQuestions->count(), // ✨ ADD THIS
        ]);
    }

    

    private function currentSelectedMarks(Paper $paper): int
    {
        return (int) DB::table('paper_question')
            ->where('paper_id', $paper->id)
            ->sum('marks');
    }


    // private function recalcTotalMarks(Paper $paper): void
    // {
    //     $sum = DB::table('paper_question')
    //         ->where('paper_id', $paper->id)
    //         ->sum('marks');

    //     $paper->total_marks = (int) $sum;
    //     $paper->save();
    // }
    
    public function attachQuestion(Paper $paper, Request $request)
{
    // 1. Initial setup (common to both modes)
    $data = $request->validate([
        'question_id' => 'required|integer|exists:questions,id',
        'rule_id'     => 'nullable|integer|exists:section_rules,id',
    ]);
    $question = Question::findOrFail($data['question_id']);

    // 2. Branch logic based on whether a rule_id is present
    
    // ===== CASE 1: Rule-based attachment (from "Fulfill Blueprint" page) =====
    if ($request->filled('rule_id')) {
        $rule = SectionRule::findOrFail($data['rule_id']);

        // Check if the question is already attached for this specific rule
        $existsForRule = $paper->questions()
            ->where('questions.id', $data['question_id'])
            ->wherePivot('section_rule_id', $rule->id)
            ->exists();

        // ✨ VALIDATION: Only check the limit if we are adding a NEW question
        if (!$existsForRule) {
            $currentMarks = $this->currentSelectedMarks($paper);
            $marksToAdd = (int) $rule->marks_per_question;
            if (($currentMarks + $marksToAdd) > $paper->total_marks) {
                return response()->json([
                    'error'   => 'Adding this question exceeds the total marks limit.',
                    'current' => $currentMarks
                ], 422);
            }
        }

        // Attach or update the pivot table entry
        if ($existsForRule) {
            $paper->questions()->updateExistingPivot($data['question_id'], [
                'marks'           => (int) $rule->marks_per_question,
                'section_rule_id' => $rule->id,
                'updated_at'      => now(),
            ]);
        } else {
            $paper->questions()->attach($data['question_id'], [
                'marks'           => (int) $rule->marks_per_question,
                'section_rule_id' => $rule->id,
                'sort_order'      => ($paper->questions()->count() + 1),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // Return a consistent, successful response with the updated total
        $finalMarks = $this->currentSelectedMarks($paper);
        return response()->json(['ok' => true, 'current' => $finalMarks]);
    } 
    
    // ===== CASE 2: Free-form attachment (from "Select Questions" page) =====
    else {
        // Check if the question already exists on the paper
        $existsAny = $paper->questions()
            ->where('questions.id', $data['question_id'])
            ->exists();

        // ✨ VALIDATION: Only check the limit if we are adding a NEW question
        if (!$existsAny) {
            $currentMarks = $this->currentSelectedMarks($paper);
            $marksToAdd = (int) $question->marks;
            if (($currentMarks + $marksToAdd) > $paper->total_marks) {
                return response()->json([
                    'error'   => 'Adding this question exceeds the total marks limit.',
                    'current' => $currentMarks
                ], 422);
            }
        }

        // Attach if it doesn't exist
        if (!$existsAny) {
            $paper->questions()->attach($data['question_id'], [
                'marks'           => (int) $question->marks,
                'section_rule_id' => null,
                'sort_order'      => ($paper->questions()->count() + 1),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // Return a consistent, successful response
        $current = $this->currentSelectedMarks($paper);
        return response()->json(['ok' => true, 'current' => $current]);
    }
}




    
    public function detachQuestion(Paper $paper, Request $request)
    {
        $data = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'rule_id'     => 'nullable|integer|exists:section_rules,id',
        ]);

        // ===== Rule mode (fulfill page) =====
        if ($request->filled('rule_id')) {
            $paper->questions()->newPivotStatement()
                ->where('paper_id', $paper->id)
                ->where('question_id', $data['question_id'])
                ->where('section_rule_id', $data['rule_id'])
                ->delete();

            $this->recalcTotalMarks($paper);
            return response()->json(['ok' => true]);
        }

        // ===== Free-form mode (select-questions page) =====
        // Only remove the row that has section_rule_id IS NULL,
        // so we don’t accidentally remove a rule-based assignment.
        $paper->questions()->newPivotStatement()
            ->where('paper_id', $paper->id)
            ->where('question_id', $data['question_id'])
            ->whereNull('section_rule_id')
            ->delete();

       $current = $this->currentSelectedMarks($paper);
        return response()->json(['ok' => true, 'current' => $current]);
    }






     public function previewAnswers(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) {
            abort(403);
        }

        // Eager load all necessary relationships for the view
        $paper->load('questions', 'blueprint.sections.rules');
        $blueprint = $paper->blueprint;

        return view('institute.papers.preview_answers', compact('paper', 'blueprint'));
    }


    public function getSubjectsForClass($classId)
    {
        // Optional: auth/role guard
        if (!auth()->check() || auth()->user()->role !== 'institute') {
            abort(403);
        }

        // Adjust FK if needed (most setups use 'class_id' on subjects)
        $subjects = Subject::where('class_id', $classId)
            ->orderBy('name')
            ->get(['id','name']);

        return response()->json($subjects);
    }



    // ADD THIS: edit form
    
    public function edit(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) {
            abort(403);
        }

        // Load what we need for the view
        $paper->load(['blueprint']); // we don't need to eager-load subject.academicClass here

        $boards   = Board::all();
        $classes  = AcademicClassModel::all();

        // Load subjects for the paper's class WITH the academicClass relation for display "(Class Name)"
        $subjects = Subject::with('academicClass')
            ->where('class_id', $paper->class_id)
            ->orderBy('name')
            ->get();

        $blueprint = $paper->blueprint;

        return view('institute.papers.edit', compact(
            'paper', 'boards', 'classes', 'subjects', 'blueprint'
        ));
    }

// ADD THIS: update handler
        public function update(Request $request, Paper $paper)
        {
            if ($paper->institute_id !== auth()->id()) {
                abort(403);
            }

            $validated = $request->validate([
                'board_id'     => 'required|exists:boards,id',
                'class_id'     => 'required|exists:academic_class_models,id',
                'subject_id'   => 'required|exists:subjects,id',
                'title'        => 'required|string|max:255',
                'total_marks'  => 'required|integer|min:1',
                'time_allowed' => 'required|string|max:50',
                'exam_date'    => 'nullable|date',
                'instructions' => 'nullable|string',
            ]);

            $paper->update($validated);

            return redirect()
                ->route('institute.papers.index')
                ->with('success', 'Paper updated successfully.');
        }

        // ADD THIS: delete handler
        public function destroy(Paper $paper)
        {
            if ($paper->institute_id !== auth()->id()) {
                abort(403);
            }

            // detach pivot rows first (safe cleanup)
            $paper->questions()->detach();

            $paper->delete();

            return redirect()
                ->route('institute.papers.index')
                ->with('success', 'Paper deleted successfully.');
        }


    // You can add other standard resource controller methods like edit, update, destroy if needed.
}