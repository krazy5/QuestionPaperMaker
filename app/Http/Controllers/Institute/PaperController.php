<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Paper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Question;
use Spatie\Browsershot\Browsershot;
use App\Models\Chapter;


class PaperController extends Controller
{
    public function index()
    {
        $instituteId = Auth::id();

            $papers = Paper::where('institute_id', $instituteId)
                            ->with('subject.academicClass')
                            ->latest()
                            ->paginate(15); // <-- Change .get() to .paginate(15)

            return view('institute.papers.index', compact('papers'));
    }

    public function create()
    {
        $boards = Board::all();
        $classes = AcademicClassModel::all();
        $subjects = Subject::with('academicClass')->get();
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
            'instructions' => 'nullable|string',
        ]);
        $validated['institute_id'] = auth()->id();
        $paper = Paper::create($validated);
        return redirect()->route('institute.papers.questions.select', $paper);
    }

      public function selectQuestions(Paper $paper)
        {
            if ($paper->institute_id !== auth()->id()) abort(403);
            
            // 1. Fetch all relevant chapters for the paper's subject
            $chapters = Chapter::where('subject_id', $paper->subject_id)->get();
            
            // 2. Fetch all relevant questions
            $questions = Question::where('board_id', $paper->board_id)
                                ->where('class_id', $paper->class_id)
                                ->where('subject_id', '>=', $paper->subject_id)
                                ->where(fn($q) => $q->where('approved', true)->orWhere('institute_id', auth()->id()))
                                ->get();
            
            // 3. Get the IDs of questions already on the paper to pre-check the boxes
            $existingQuestionIds = $paper->questions->pluck('id');

            // 4. Pass all data to the new view
            return view('institute.papers.select_questions', compact('paper', 'chapters', 'questions', 'existingQuestionIds'));
        }
    
    public function saveQuestions(Request $request, Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        $questionIds = $request->input('questions', []);
        $questions = Question::find($questionIds);

        $syncData = [];
        $totalMarks = 0;
        foreach ($questions as $question) {
            $syncData[$question->id] = ['marks' => $question->marks];
            $totalMarks += $question->marks;
        }

        $paper->questions()->sync($syncData);
        $paper->total_marks = $totalMarks;
        $paper->save();

        return redirect()->route('dashboard')->with('success', 'Paper saved successfully!');
    }

    public function edit(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        // We need this data for the dropdowns, just like in create()
        $boards = Board::all();
        $classes = AcademicClassModel::all();
        $subjects = Subject::with('academicClass')->get();

        return view('institute.papers.edit', compact('paper', 'boards', 'classes', 'subjects'));
    }
    
    // ADD THIS NEW update() method
    public function update(Request $request, Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:academic_class_models,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        $paper->update($validated);

        return redirect()->route('dashboard')->with('success', 'Paper details updated successfully!');
    }

    public function destroy(Paper $paper)
    {
        if ($paper->institute_id !== auth()->id()) abort(403);

        $paper->delete();

        return redirect()->route('dashboard')->with('success', 'Paper deleted successfully.');
    }

    public function download(Paper $paper)
    {
        // ADD THIS LINE AT THE TOP
    ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

        if ($paper->institute_id !== auth()->id()) abort(403);
        
        $paper->load('questions');
        $html = view('pdfs.paper', compact('paper'))->render();

        $pdf = Browsershot::html($html)
                ->timeout(120)
                ->delay(2000)
                ->noSandbox()
                ->pdf();
        
        $filename = \Illuminate\Support\Str::slug($paper->title) . '.pdf';
        return response()->streamDownload(fn() => print($pdf), $filename);
    }

    // ADD THIS NEW METHOD
    public function preview(Paper $paper)
    {
        // Security check
        if ($paper->institute_id !== auth()->id()) {
            abort(403);
        }
        
        $paper->load('questions');

        return view('institute.papers.preview', compact('paper'));
    }


    public function previewAnswers(Paper $paper)
    {
        // Security check to ensure the user owns the paper
        if ($paper->institute_id !== auth()->id()) {
            abort(403);
        }
        
        // Load the paper with its questions
        $paper->load('questions');

        // Return the new answer key view
        return view('institute.papers.preview_answers', compact('paper'));
    }


}