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
    public function index(Request $request)
    {
        $q            = trim((string) $request->input('q'));
        $boardId      = $request->integer('board_id');
        $classId      = $request->input('class_id');       // may be string
        $subjectId    = $request->input('subject_id');     // may be string
        $questionType = $request->input('question_type');  // mcq|short|long|true_false
        $difficulty   = $request->input('difficulty');     // easy|medium|hard

        $query = \App\Models\Question::with(['board','chapter'])
            ->when($q !== '', fn($qq) => $qq->where('question_text', 'like', "%{$q}%"))
            ->when($boardId, fn($qq) => $qq->where('board_id', $boardId))
            // JSON array columns
            ->when($classId, fn($qq) => $qq->whereJsonContains('class_id', (int) $classId))
            ->when($subjectId, fn($qq) => $qq->whereJsonContains('subject_id', (int) $subjectId))
            ->when(in_array($questionType, ['mcq','short','long','true_false'], true), fn($qq) => $qq->where('question_type', $questionType))
            ->when(in_array($difficulty, ['easy','medium','hard'], true), fn($qq) => $qq->where('difficulty', $difficulty))
            ->latest();

        $questions = $query->paginate(12)->withQueryString();

        $boards  = \App\Models\Board::orderBy('name')->get(['id','name']);
        $classes = \App\Models\AcademicClassModel::orderBy('name')->get(['id','name']);

        return view('admin.questions.index', compact('questions','boards','classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $boards  = Board::all();
        $classes = AcademicClassModel::all();

        // If your create view needs subjects/chapters via AJAX based on class/subject,
        // you can keep it minimal here. Otherwise, preload:
        // $subjects = Subject::orderBy('name')->get();
        // $chapters = Chapter::orderBy('name')->get();

        return view('admin.questions.create', compact('boards', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'board_id'       => 'required|exists:boards,id',
            'class_id'       => 'required|exists:academic_class_models,id',
            'subject_id'     => 'required|exists:subjects,id',
            'chapter_id'     => 'required|exists:chapters,id',
            'question_text'  => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'question_type'  => 'required|in:mcq,short,long,true_false',
            'options'        => 'required_if:question_type,mcq|array|min:2',
            'options.*'      => 'required_if:question_type,mcq|string|nullable',
            'correct_answer' => 'required_if:question_type,mcq|string',
            'answer_text'    => 'nullable|string',
            'answer_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'solution_text'  => 'nullable|string',
            'marks'          => 'required|integer|min:1',
            'difficulty'     => 'required|in:easy,medium,hard',
        ]);

        // Images
        if ($request->hasFile('question_image')) {
            $validated['question_image_path'] = $request->file('question_image')
                ->store('question_images', 'public');
        }
        if ($request->hasFile('answer_image')) {
            $validated['answer_image_path'] = $request->file('answer_image')
                ->store('answer_images', 'public');
        }

        // Ensure JSON-array consistency for these columns
        $validated['class_id']   = [ (int) $validated['class_id'] ];
        $validated['subject_id'] = [ (int) $validated['subject_id'] ];

        // Let the model accessor/mutator handle JSON for 'options'
        if ($request->question_type === 'mcq') {
            // Clean & reindex options
            $opts = array_values(array_filter($request->input('options', []), static fn($v) => $v !== null && $v !== ''));
            $validated['options'] = $opts;
        } else {
            $validated['options']        = null;
            $validated['correct_answer'] = null;
        }

        $validated['source']   = 'admin';
        $validated['approved'] = true;

        Question::create($validated);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $boards   = Board::all();
        $classes  = AcademicClassModel::all();
        $subjects = Subject::with('academicClass')->get();
        $chapters = Chapter::with('subject')->get();

        return view('admin.questions.edit', compact('question', 'boards', 'classes', 'subjects', 'chapters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'board_id'       => 'required|exists:boards,id',
            'class_id'       => 'required|exists:academic_class_models,id',
            'subject_id'     => 'required|exists:subjects,id',
            'chapter_id'     => 'required|exists:chapters,id',
            'question_text'  => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'question_type'  => 'required|in:mcq,short,long,true_false',
            'options'        => 'required_if:question_type,mcq|array|min:2',
            'options.*'      => 'required_if:question_type,mcq|string|nullable',
            'correct_answer' => 'required_if:question_type,mcq|string',
            'answer_text'    => 'nullable|string',
            'answer_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'solution_text'  => 'nullable|string',
            'marks'          => 'required|integer|min:1',
            'difficulty'     => 'required|in:easy,medium,hard',
        ]);

        // Replace images if new ones uploaded
        if ($request->hasFile('question_image')) {
            if ($question->question_image_path) {
                Storage::disk('public')->delete($question->question_image_path);
            }
            $validated['question_image_path'] = $request->file('question_image')
                ->store('question_images', 'public');
        }
        if ($request->hasFile('answer_image')) {
            if ($question->answer_image_path) {
                Storage::disk('public')->delete($question->answer_image_path);
            }
            $validated['answer_image_path'] = $request->file('answer_image')
                ->store('answer_images', 'public');
        }

        // Ensure JSON-array consistency for these columns
        $validated['class_id']   = [ (int) $validated['class_id'] ];
        $validated['subject_id'] = [ (int) $validated['subject_id'] ];

        // Handle options via model mutator
        if ($request->question_type === 'mcq') {
            $opts = array_values(array_filter($request->input('options', []), static fn($v) => $v !== null && $v !== ''));
            $validated['options'] = $opts;
            $validated['answer_text'] = null;
        } else {
            $validated['options']        = null;
            $validated['correct_answer'] = null;
        }

        $question->update($validated);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully!');
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

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully!');
    }
}
