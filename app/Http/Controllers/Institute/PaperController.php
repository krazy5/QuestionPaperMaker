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
use App\Exceptions\MarksLimitExceededException;
use App\Services\PaperService;


class PaperController extends Controller
{
    protected PaperService $paperService;

    public function __construct(PaperService $paperService)
    {
        $this->paperService = $paperService;
    }

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

        $validated = $request->validate([
            'exam_date' => 'required|date',
        ]);

        $paper = $this->paperService->createPaperFromBlueprint(
            $blueprint,
            $validated['exam_date'],
            (int) auth()->id()
        );

        return redirect()
            ->route('institute.papers.preview', $paper)
            ->with('success', 'Paper created and auto-filled successfully!');
    }

    public function autoFillBlueprint(Paper $paper, Request $request)
    {
        if ($paper->institute_id !== auth()->id()) {
            abort(403);
        }

        $selectedChapterIds = $request->has('chapters')
            ? array_map('intval', (array) $request->input('chapters', []))
            : [];

        $this->paperService->autoFillPaper($paper, $selectedChapterIds, true);

        return back()->with('status', 'Auto-filled successfully.');
    }






    // --- OLDER MANUAL WORKFLOW & HELPER METHODS ---

    public function create()
    {
        $boards = Board::orderBy('name')->get();

        $oldBoardId = request()->old('board_id');
        $selectedBoardId = $oldBoardId !== null
            ? (int) $oldBoardId
            : ($boards->first()->id ?? null);

        return view('institute.papers.create', compact(
            'boards',
            'selectedBoardId'
        ));
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

            // 🔎 Detect a matching ADMIN (or global) blueprint via the service
            $matchingBlueprint = $this->paperService->findMatchingBlueprint($paper);

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

            $query = $this->paperService->candidateQueryForRule($paper, $rule, $chapterIds);

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
        public function fulfillBlueprint(Paper $paper)
        {
            if ($paper->institute_id !== auth()->id()) {
                abort(403);
            }

            // Prefer the blueprint already linked to the paper
            $blueprint = $paper->blueprint;

            // Optional fallback: try to find a matching blueprint by context
            if (!$blueprint) {
                $blueprint = $this->paperService->findMatchingBlueprint($paper);

                if (!$blueprint) {
                    return redirect()
                        ->route('institute.papers.questions.select', $paper)
                        ->with('error', 'No matching blueprint found for this paper.');
                }

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

    public function attachQuestion(Paper $paper, Request $request)
    {
        $data = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'rule_id'     => 'nullable|integer|exists:section_rules,id',
        ]);

        $question = Question::findOrFail($data['question_id']);
        $rule = $request->filled('rule_id')
            ? SectionRule::findOrFail($data['rule_id'])
            : null;

        try {
            $current = $this->paperService->attachQuestion($paper, $question, $rule);
        } catch (MarksLimitExceededException $exception) {
            return response()->json([
                'error'   => $exception->getMessage(),
                'current' => $exception->getCurrentMarks(),
            ], 422);
        }

        return response()->json(['ok' => true, 'current' => $current]);
    }




    
    public function detachQuestion(Paper $paper, Request $request)
    {
        $data = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'rule_id'     => 'nullable|integer|exists:section_rules,id',
        ]);

        $question = Question::findOrFail($data['question_id']);
        $rule = $request->filled('rule_id')
            ? SectionRule::findOrFail($data['rule_id'])
            : null;

        $current = $this->paperService->detachQuestion($paper, $question, $rule);

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


    public function getClassesForBoard($boardId)
    {
        // if (!auth()->check() || auth()->user()->role !== 'institute') {
        //     abort(403);
        // }

        return response()->json(
            $this->paperService->getClassesForBoard((int) $boardId)
        );
    }

    public function getSubjectsForClass($classId)
    {
        // if (!auth()->check() || auth()->user()->role !== 'institute') {
        //     abort(403);
        // }

        return response()->json(
            $this->paperService->getSubjectsForClass((int) $classId)
        );
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
