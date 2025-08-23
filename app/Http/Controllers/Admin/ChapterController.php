<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicClassModel;
use App\Models\Subject;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        // Per-page (safe list)
        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100], true)) {
            $perPage = 15;
        }

        // Sort (safe list)
        $sort = $request->input('sort', 'newest');
        if (!in_array($sort, ['newest', 'name_asc', 'name_desc'], true)) {
            $sort = 'newest';
        }

        $search    = trim((string)$request->input('search', ''));
        $classId   = $request->input('class_id');
        $subjectId = $request->input('subject_id');

        $query = Chapter::query()->with(['subject.academicClass']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhereHas('academicClass', function ($q3) use ($search) {
                             $q3->where('name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if (!empty($classId)) {
            $query->whereHas('subject', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if (!empty($subjectId)) {
            $query->where('subject_id', $subjectId);
        }

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
        }

        $chapters = $query->paginate($perPage)->withQueryString();

        // For filters
        $classes = AcademicClassModel::orderBy('name')->get(['id', 'name']);
        $subjectsForFilter = collect();
        if (!empty($classId)) {
            $subjectsForFilter = Subject::where('class_id', $classId)->orderBy('name')->get(['id', 'name', 'class_id']);
        } else {
            // Optional: keep empty to avoid huge lists
            $subjectsForFilter = Subject::orderBy('name')->limit(50)->get(['id', 'name', 'class_id']);
        }

        return view('admin.chapters.index', compact('chapters', 'classes', 'subjectsForFilter'));
    }

    public function create()
    {
        $classes = AcademicClassModel::orderBy('name')->get(['id', 'name']);
        return view('admin.chapters.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        Chapter::create($validated);

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter created successfully!');
    }

    public function edit(Chapter $chapter)
    {
        $classes  = AcademicClassModel::orderBy('name')->get(['id', 'name']);
        // Preload subjects of the current class for initial render
        $currentClassId = $chapter->subject->class_id ?? $chapter->subject->academicClass->id ?? null;

        $subjects = Subject::when($currentClassId, fn($q) => $q->where('class_id', $currentClassId))
            ->orderBy('name')
            ->get(['id', 'name', 'class_id']);

        return view('admin.chapters.edit', compact('chapter', 'classes', 'subjects'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $chapter->update($validated);

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter updated successfully!');
    }

    public function destroy(Chapter $chapter)
    {
        $chapter->delete();

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter deleted successfully!');
    }
}
