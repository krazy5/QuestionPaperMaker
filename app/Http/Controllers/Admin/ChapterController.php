<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Subject; // <-- We need this model for the dropdown
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::with('subject.academicClass')->latest()->get();
        return view('admin.chapters.index', compact('chapters'));
    }

    public function create()
    {
        $subjects = Subject::with('academicClass')->get();
        return view('admin.chapters.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ]);
        Chapter::create($validated);
        return redirect()->route('chapters.index')->with('success', 'Chapter created successfully!');
    }

    public function edit(Chapter $chapter)
    {
        $subjects = Subject::with('academicClass')->get();
        return view('admin.chapters.edit', compact('chapter', 'subjects'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ]);
        $chapter->update($validated);
        return redirect()->route('chapters.index')->with('success', 'Chapter updated successfully!');
    }

    public function destroy(Chapter $chapter)
    {
        $chapter->delete();
        return redirect()->route('chapters.index')->with('success', 'Chapter deleted successfully!');
    }
}