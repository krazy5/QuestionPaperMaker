<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = AcademicClassModel::latest()->get();
        return view('admin.classes.index', ['classes' => $classes]);
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:academic_class_models']);
        AcademicClassModel::create($validated);
        return redirect()->route('admin.classes.index')->with('success', 'Class created successfully!');
    }

    public function edit(AcademicClassModel $class)
    {
        return view('admin.classes.edit', ['class' => $class]);
    }

    public function update(Request $request, AcademicClassModel $class)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:academic_class_models,name,' . $class->id]);
        $class->update($validated);
        return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully!');
    }

    public function destroy(AcademicClassModel $class)
    {
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Class deleted successfully!');
    }
}