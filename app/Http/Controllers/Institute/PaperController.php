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
use Exception;
use Illuminate\Support\Facades\Log;

class PaperController extends Controller
{
    // ... index method ...
    public function index()
    {
        $user = auth()->user();
        $activeSubscription = $user->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->latest('starts_at')->first();
        $papers = Paper::where('institute_id', $user->id)->with('subject.academicClass')->latest()->paginate(15);
        return view('institute.papers.index', compact('papers', 'activeSubscription'));
    }

    public function create()
    {
        $boards = Board::all();
        $classes = AcademicClassModel::all();
        // We no longer need to load all subjects here, as they will be fetched dynamically.
        // We pass an empty array so the view doesn't break.
        $subjects = []; 
        return view('institute.papers.create', compact('boards', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:academic_class_models,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1',
            'time_allowed' => 'required|string|max:50',
            'exam_date' => 'nullable|date',
            'instructions' => 'nullable|string',
        ]);
        $validated['institute_id'] = auth()->id();
        $paper = Paper::create($validated);
        $blueprint = PaperBlueprint::where('board_id', $paper->board_id)->where('class_id', $paper->class_id)->first();
        if ($blueprint) {
            return redirect()->route('institute.papers.fulfill_blueprint', $paper);
        }
        return redirect()->route('institute.papers.questions.select', $paper);
    }

    // ✅ ===================================================================
    // ✅ NEW METHOD TO FETCH SUBJECTS DYNAMICALLY
    // ✅ ===================================================================
    /**
     * Fetch subjects for a given class and return as JSON.
     *
     * @param  int  $classId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubjectsForClass($classId)
    {
        // This assumes your Subject model has a 'class_id' column.
        // Based on your other code, it looks like the column is 'class_id' on the subjects table.
        // If your column is named differently (e.g., 'academic_class_model_id'), update it here.
        $subjects = Subject::where('class_id', $classId)->get(['id', 'name']);

        return response()->json($subjects);
    }
    // ===================================================================
    // END OF NEW METHOD
    // ===================================================================


    
    public function getQuestionsForRule(Request $request, $paperId, $ruleId)
    {
        try {
            $paper = Paper::findOrFail($paperId);
            $rule = SectionRule::findOrFail($ruleId);

            if ($paper->institute_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Start the query
            $query = Question::where('board_id', $paper->board_id)
                                ->where('class_id', $paper->class_id)
                                ->where('subject_id', $paper->subject_id)
                                ->where('question_type', $rule->question_type)
                                ->where('marks', $rule->marks_per_question)
                                ->where(function ($query) {
                                    $query->where('approved', true)
                                          ->orWhere('institute_id', auth()->id());
                                });
            
            // --- NEW: Apply chapter filter if provided ---
            $chapterIds = $request->input('chapters', []);
            if (!empty($chapterIds)) {
                $query->whereIn('chapter_id', $chapterIds);
            }

            $availableQuestions = $query->get();
            $selectedQuestionIds = $paper->questions()->pluck('questions.id');

            return response()->json([
                'available_questions' => $availableQuestions,
                'selected_ids' => $selectedQuestionIds,
            ]);

        } catch (Exception $e) {
            Log::error("Error in getQuestionsForRule: " . $e->getMessage());
            return response()->json(['error' => 'An internal server error occurred.'], 500);
        }
    }

    // --- UPDATED: This method now fetches chapters ---
    public function fulfillBlueprint(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) {
            abort(403);
        }
        $blueprint = PaperBlueprint::with('sections.rules')
                                    ->where('board_id', $paper->board_id)
                                    ->where('class_id', $paper->class_id)
                                    ->firstOrFail();
        
        // Fetch chapters for the filter
        $chapters = Chapter::where('subject_id', $paper->subject_id)->get();

        return view('institute.papers.fulfill_blueprint', compact('paper', 'blueprint', 'chapters'));
    }

    // --- NEW: Magic Wand "Auto-fill" Logic ---
    public function autoFillBlueprint(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        $blueprint = PaperBlueprint::with('sections.rules')->where('board_id', $paper->board_id)->where('class_id', $paper->class_id)->firstOrFail();
        
        $allQuestionsToAttach = [];

        foreach ($blueprint->sections as $section) {
            foreach ($section->rules as $rule) {
                $questions = Question::where('board_id', $paper->board_id)
                                        ->where('class_id', $paper->class_id)
                                        ->where('subject_id', $paper->subject_id)
                                        ->where('question_type', $rule->question_type)
                                        ->where('marks', $rule->marks_per_question)
                                        ->where(fn($q) => $q->where('approved', true)->orWhere('institute_id', auth()->id()))
                                        ->inRandomOrder()
                                        ->limit($rule->number_of_questions_to_select)
                                        ->get();

                foreach ($questions as $question) {
                    $allQuestionsToAttach[$question->id] = ['marks' => $question->marks];
                }
            }
        }

        // Sync all collected questions at once, replacing any old ones
        $paper->questions()->sync($allQuestionsToAttach);

        return redirect()->route('institute.papers.fulfill_blueprint', $paper)->with('success', 'Paper has been auto-filled successfully!');
    }

    // --- NEW: API method to get updated counts ---
    public function getPaperStats(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        $stats = [];
        $paper->load('questions'); // Eager load questions with pivot data
        $blueprint = PaperBlueprint::with('sections.rules')->where('board_id', $paper->board_id)->where('class_id', $paper->class_id)->firstOrFail();

        foreach ($blueprint->sections as $section) {
            foreach ($section->rules as $rule) {
                // Filter the loaded questions to count how many match this rule's criteria
                $count = $paper->questions->filter(function ($question) use ($rule) {
                    return $question->question_type == $rule->question_type &&
                           $question->pivot->marks == $rule->marks_per_question;
                })->count();
                $stats[$rule->id] = $count;
            }
        }
        return response()->json($stats);
    }
    
    // ... other methods ...
    public function selectQuestions(Request $request, Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);
        $chapters = Chapter::where('subject_id', $paper->subject_id)->get();
        $query = Question::where('board_id', $paper->board_id)->where('class_id', $paper->class_id)->where('subject_id', $paper->subject_id)->where(fn($q) => $q->where('approved', true)->orWhere('institute_id', auth()->id()));
        if ($request->filled('chapter') && $request->chapter != 'all') {
            $query->where('chapter_id', $request->chapter);
        }
        if ($request->filled('types')) {
            $query->whereIn('question_type', $request->types);
        }
        $questions = $query->latest()->paginate(10)->withQueryString();
        $existingQuestionIds = $paper->questions->pluck('id');
        $currentMarks = $paper->questions()->sum('paper_questions.marks');
        return view('institute.papers.select_questions', [
            'paper' => $paper,
            'chapters' => $chapters,
            'questions' => $questions,
            'existingQuestionIds' => $existingQuestionIds,
            'currentChapter' => $request->chapter ?? 'all',
            'currentTypes' => $request->types ?? ['mcq', 'long', 'short', 'true_false'],
            'currentMarks' => $currentMarks,
        ]);
    }
    
    public function attachQuestion(Request $request, Paper $paper, Question $question)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);
        $paper->questions()->syncWithoutDetaching([$question->id => ['marks' => $question->marks]]);
        return response()->json(['status' => 'success']);
    }

    public function detachQuestion(Request $request, Paper $paper, Question $question)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);
        $paper->questions()->detach($question->id);
        return response()->json(['status' => 'success']);
    }

        // --- THIS IS THE UPDATED EDIT METHOD ---
        public function edit(Paper $paper)
        {
            if ($paper->institute_id !== auth()->id()) abort(403);

            // Check if a blueprint exists for this paper's board and class
            $blueprint = PaperBlueprint::where('board_id', $paper->board_id)
                                        ->where('class_id', $paper->class_id)
                                        ->first();

            $boards = Board::all();
            $classes = AcademicClassModel::all();
            $subjects = Subject::with('academicClass')->get();

            // Pass the blueprint (or null if it doesn't exist) to the view
            return view('institute.papers.edit', compact('paper', 'boards', 'classes', 'subjects', 'blueprint'));
        }

    public function update(Request $request, Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);
        $validated = $request->validate(['board_id' => 'required|exists:boards,id', 'class_id' => 'required|exists:academic_class_models,id', 'subject_id' => 'required|exists:subjects,id', 'title' => 'required|string|max:255', 'total_marks' => 'required|integer|min:1', 'time_allowed' => 'required|string|max:50','exam_date' => 'nullable|date', 'instructions' => 'nullable|string']);
        $paper->update($validated);
        return redirect()->route('institute.dashboard')->with('success', 'Paper details updated successfully!');
    }

    public function destroy(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);
        $paper->delete();
        return redirect()->route('institute.dashboard')->with('success', 'Paper deleted successfully.');
    }

    public function preview(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        // Load the paper's questions
        $paper->load('questions');
        
        // Also load the blueprint for this paper
        $blueprint = PaperBlueprint::with('sections.rules')
                                    ->where('board_id', $paper->board_id)
                                    ->where('class_id', $paper->class_id)
                                    ->first();

        return view('institute.papers.preview', compact('paper', 'blueprint'));
    }

    public function previewAnswers(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        $paper->load('questions');
        
        $blueprint = PaperBlueprint::with('sections.rules')
                                    ->where('board_id', $paper->board_id)
                                    ->where('class_id', $paper->class_id)
                                    ->first();

        return view('institute.papers.preview_answers', compact('paper', 'blueprint'));
    }
}
