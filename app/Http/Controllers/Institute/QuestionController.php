<?php

namespace App\Http\Controllers\Institute;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Chapter;
use Illuminate\Support\Facades\Storage; // ✅ Import Storage facade

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
            // ✅ Add image validation rules
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'answer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('question_image')) {
            $validated['question_image_path'] = $request->file('question_image')->store('question_images', 'public');
        }
        if ($request->hasFile('answer_image')) {
            $validated['answer_image_path'] = $request->file('answer_image')->store('answer_images', 'public');
        }

        // ✅ Handle Question Image Upload
            // if ($request->hasFile('question_image')) {
            //     $path = $request->file('question_image')->store('public/questions');
            //     $validated['question_image_path'] = Storage::url($path); // <-- CORRECT KEY
            // }

            // // ✅ Handle Solution Image Upload
            // if ($request->hasFile('solution_image')) {
            //     $path = $request->file('solution_image')->store('public/questions');
            //     $validated['answer_image_path'] = Storage::url($path); // <-- CORRECT KEY
            // }

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
            // ✅ Add image validation rules
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'answer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ✅ Handle Question Image Update
        if ($request->hasFile('question_image')) {
            // Delete old image if it exists
            if ($question->question_image) {
                Storage::delete(str_replace('/storage', 'public', $question->question_image));
            }
            $path = $request->file('question_image')->store('public/questions');
            $validated['question_image_path'] = Storage::url($path);
        }

        // ✅ Handle Solution Image Update
        if ($request->hasFile('solution_image')) {
            // Delete old image if it exists
            if ($question->solution_image) {
                Storage::delete(str_replace('/storage', 'public', $question->solution_image));
            }
            $path = $request->file('solution_image')->store('public/questions');
            $validated['answer_image_path'] = Storage::url($path);
        }

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

        // ✅ Delete images from storage before deleting the record
        if ($question->question_image) {
            Storage::delete(str_replace('/storage', 'public', $question->question_image));
        }
        if ($question->solution_image) {
            Storage::delete(str_replace('/storage', 'public', $question->solution_image));
        }

        $question->delete();

        return redirect()->route('institute.questions.index')->with('success', 'Question deleted successfully.');
    }
}
