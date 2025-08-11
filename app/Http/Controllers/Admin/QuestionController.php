<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::with('subject.academicClass', 'chapter', 'board')->latest()->paginate(15);
        return view('admin.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $boards = Board::all();
        $classes = AcademicClassModel::all();
        return view('admin.questions.create', compact('boards', 'classes'));
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
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'question_type' => 'required|in:mcq,short,long,true_false',
            'options' => 'required_if:question_type,mcq|array|min:2',
            'options.*' => 'required_if:question_type,mcq|string|nullable',
            'correct_answer' => 'required_if:question_type,mcq|string',
            'answer_text' => 'nullable|string',
            'answer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'solution_text' => 'nullable|string',
            'marks' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        if ($request->hasFile('question_image')) {
            $validated['question_image_path'] = $request->file('question_image')->store('question_images', 'public');
        }
        if ($request->hasFile('answer_image')) {
            $validated['answer_image_path'] = $request->file('answer_image')->store('answer_images', 'public');
        }
        if ($request->question_type === 'mcq') {
            $validated['options'] = json_encode($request->options);
        }
        
        $validated['source'] = 'admin';
        $validated['approved'] = true;

        Question::create($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Question added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
        {
            $boards = Board::all();
            $classes = AcademicClassModel::all();
            
            // ✅ ADD THESE TWO LINES TO FETCH THE MISSING DATA
            $subjects = Subject::with('academicClass')->get();
            $chapters = Chapter::with('subject')->get();

            // ✅ ADD THE NEW VARIABLES TO THE COMPACT FUNCTION
            return view('admin.questions.edit', compact('question', 'boards', 'classes', 'subjects', 'chapters'));
        }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:academic_class_models,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'required|exists:chapters,id',
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'question_type' => 'required|in:mcq,short,long,true_false',
            'options' => 'required_if:question_type,mcq|array|min:2',
            'options.*' => 'required_if:question_type,mcq|string|nullable',
            'correct_answer' => 'required_if:question_type,mcq|string',
            'answer_text' => 'nullable|string',
            'answer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'solution_text' => 'nullable|string',
            'marks' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        if ($request->hasFile('question_image')) {
            // Delete the old image if it exists
            if ($question->question_image_path) {
                Storage::disk('public')->delete($question->question_image_path);
            }
            $validated['question_image_path'] = $request->file('question_image')->store('question_images', 'public');
        }

        if ($request->hasFile('answer_image')) {
            // Delete the old image if it exists
            if ($question->answer_image_path) {
                Storage::disk('public')->delete($question->answer_image_path);
            }
            $validated['answer_image_path'] = $request->file('answer_image')->store('answer_images', 'public');
        }

        if ($request->question_type === 'mcq') {
            $validated['options'] = json_encode($request->options);
            $validated['answer_text'] = null;
        } else {
            $validated['options'] = null;
            $validated['correct_answer'] = null;
        }

        $question->update($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        if ($question->question_image_path) {
            Storage::disk('public')->delete($question->question_image_path);
        }
        if ($question->answer_image_path) {
            Storage::disk('public')->delete($question->answer_image_path);
        }
        
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully!');
    }
}
