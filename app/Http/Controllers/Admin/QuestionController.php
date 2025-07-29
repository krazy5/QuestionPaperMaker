<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Question;


class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load relationships for efficiency
        $questions = Question::with('subject.academicClass', 'chapter', 'board')->latest()->get();

        return view('admin.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //// Fetch all the data needed for the form's dropdowns
            // We only need to load the top-level items now
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
                'question_type' => 'required|in:mcq,short,long,true_false',
                'marks' => 'required|integer|min:1',
                'difficulty' => 'required|in:easy,medium,hard',
                'answer_text' => 'nullable|string',
                'solution_text' => 'nullable|string',
                // Add validation for MCQ fields, only if the question type is 'mcq'
                'options' => 'required_if:question_type,mcq|array|min:2',
                'options.*' => 'required_if:question_type,mcq|string',
                'correct_answer' => 'required_if:question_type,mcq|string',
            ]);

            // If the question is an MCQ, encode the options array to JSON
            if ($request->question_type === 'mcq') {
                $validated['options'] = json_encode($request->options);
            }
            
            $validated['source'] = 'admin';
            $validated['approved'] = true;

            Question::create($validated);

            return redirect()->route('questions.index')->with('success', 'Question added successfully!');
        }
    /**
         * Show the form for editing the specified resource.
         */
       public function edit(Question $question)
       {
                // Fetch all the data needed for the form's dropdowns
                $boards = Board::all();
                $classes = AcademicClassModel::all();
                $subjects = Subject::with('academicClass')->get();
                $chapters = Chapter::with('subject')->get();

                // Pass the specific question and all the dropdown data to the view
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
                'question_type' => 'required|in:mcq,short,long,true_false',
                'marks' => 'required|integer|min:1',
                'difficulty' => 'required|in:easy,medium,hard',
                'answer_text' => 'nullable|string',
                'solution_text' => 'nullable|string',
                'options' => 'required_if:question_type,mcq|array|min:2',
                'options.*' => 'required_if:question_type,mcq|string|nullable',
                'correct_answer' => 'required_if:question_type,mcq|string',
            ]);

            // Handle options based on question type
            if ($request->question_type === 'mcq') {
                $validated['options'] = json_encode($request->options);
                $validated['answer_text'] = null; // Clear answer_text for MCQs
            } else {
                $validated['options'] = null;
                $validated['correct_answer'] = null;
            }

            $question->update($validated);

            return redirect()->route('questions.index')->with('success', 'Question updated successfully!');
        }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

   
   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        // Delete the question from the database
        $question->delete();

        // Redirect back to the question list with a success message
        return redirect()->route('questions.index')->with('success', 'Question deleted successfully!');
    }
}
