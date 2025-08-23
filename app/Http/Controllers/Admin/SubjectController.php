<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\AcademicClassModel;
use Illuminate\Http\Request;

class SubjectController extends Controller
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

    $search  = trim((string) $request->input('search', ''));
    $classId = $request->input('class_id');

    $query = Subject::query()->with('academicClass');

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('academicClass', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }

    if (!empty($classId)) {
        $query->where('class_id', $classId);
    }

    switch ($sort) {
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        default: // newest
            $query->latest();
            break;
    }

    $subjects = $query->paginate($perPage)->withQueryString();
    $classes  = AcademicClassModel::orderBy('name')->get(['id', 'name']);

    return view('admin.subjects.index', compact('subjects', 'classes'));
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
