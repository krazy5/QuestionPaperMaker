<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\AcademicClassModel;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        // Change get() to paginate(15)
        $subjects = Subject::with('academicClass')->latest()->paginate(15);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $classes = AcademicClassModel::all();
        return view('admin.subjects.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:academic_class_models,id',
        ]);
        Subject::create($validated);
        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully!');
    }

    public function edit(Subject $subject)
    {
        $classes = AcademicClassModel::all();
        return view('admin.subjects.edit', compact('subject', 'classes'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:academic_class_models,id',
        ]);
        $subject->update($validated);
        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully!');
    }
}
