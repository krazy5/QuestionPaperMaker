<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Chapter;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::where('institute_id', auth()->id())
                            ->with('subject.academicClass', 'chapter')
                            ->latest()
                            ->paginate(15);

        return view('institute.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $boards = Board::all();
        $classes = AcademicClassModel::all();
        return view('institute.questions.create', compact('boards', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:academic_class_models,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,short,long,true_false',
            'marks' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'answer_text' => 'nullable|string',
            'solution_text' => 'nullable|string',
            'options' => 'required_if:question_type,mcq|array|min:2',
            'options.*' => 'required_if:question_type,mcq|string|nullable',
            'correct_answer' => 'required_if:question_type,mcq|string',
        ]);

        $validated['institute_id'] = auth()->id();
        $validated['source'] = 'institute';
        $validated['approved'] = true; 

        if ($request->question_type === 'mcq') {
            $validated['options'] = json_encode($request->options);
        }

        Question::create($validated);

        return redirect()->route('institute.questions.index')->with('success', 'Question added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        // Security Check: Ensure the institute owns this question
        if ($question->institute_id !== auth()->id()) {
            abort(403);
        }

        $boards = Board::all();
        $classes = AcademicClassModel::all();
        // We need all subjects and chapters for the dropdowns, as they might change them
        $subjects = Subject::with('academicClass')->get();
        $chapters = Chapter::with('subject')->get();

        return view('institute.questions.edit', compact('question', 'boards', 'classes', 'subjects', 'chapters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        // Security Check
        if ($question->institute_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:academic_class_models,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,short,long,true_false',
            'marks' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'answer_text' => 'nullable|string',
            'solution_text' => 'nullable|string',
            'options' => 'required_if:question_type,mcq|array|min:2',
            'options.*' => 'required_if:question_type,mcq|string|nullable',
            'correct_answer' => 'required_if:question_type,mcq|string',
        ]);

        if ($request->question_type === 'mcq') {
            $validated['options'] = json_encode($request->options);
            $validated['answer_text'] = null;
        } else {
            $validated['options'] = null;
            $validated['correct_answer'] = null;
        }

        $question->update($validated);

        return redirect()->route('institute.questions.index')->with('success', 'Question updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        // Security Check
        if ($question->institute_id !== auth()->id()) {
            abort(403);
        }

        $question->delete();

        return redirect()->route('institute.questions.index')->with('success', 'Question deleted successfully.');
    }
}
