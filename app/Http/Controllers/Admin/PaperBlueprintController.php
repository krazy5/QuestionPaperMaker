<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaperBlueprint;
use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\AcademicClassModel;
use App\Models\BlueprintSection;
use App\Models\SectionRule;

class PaperBlueprintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $blueprints = PaperBlueprint::with('board', 'academicClass')->latest()->paginate(15);
        return view('admin.blueprints.index', compact('blueprints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all boards and classes for the form dropdowns
        $boards = Board::all();
        $classes = AcademicClassModel::all();
        return view('admin.blueprints.create', compact('boards', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:academic_class_models,id',
        ]);

        $blueprint = PaperBlueprint::create($validated);

        // Redirect to the 'show' page for the new blueprint so the admin can add sections
        return redirect()->route('admin.blueprints.show', $blueprint)
                         ->with('success', 'Blueprint created successfully! Now you can add sections.');
    }   

    /**
     * Display the specified resource.
     */
    public function show(PaperBlueprint $blueprint)
    {
        // Eager load the sections and their rules for efficiency
        $blueprint->load('sections.rules');
        
        return view('admin.blueprints.show', compact('blueprint'));
    }


    /**
     * Store a new section for the blueprint.
     */
    public function storeSection(Request $request, PaperBlueprint $blueprint)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        // Calculate sort order to add the new section at the end
        $sortOrder = $blueprint->sections()->count() + 1;

        $blueprint->sections()->create([
            'name' => $validated['name'],
            'instructions' => $validated['instructions'],
            'sort_order' => $sortOrder,
        ]);

        return redirect()->route('admin.blueprints.show', $blueprint)
                         ->with('success', 'Section added successfully!');
    }


    // ADD THIS ENTIRE NEW METHOD
    public function storeRule(Request $request, BlueprintSection $section)
    {
        $validated = $request->validate([
            'question_type' => 'required|in:mcq,short,long,true_false',
            'marks_per_question' => 'required|integer|min:1',
            'number_of_questions_to_select' => 'required|integer|min:1',
            'total_questions_to_display' => 'nullable|integer|gte:number_of_questions_to_select',
        ]);

        $section->rules()->create($validated);

        return redirect()->route('admin.blueprints.show', $section->paper_blueprint_id)
                         ->with('success', 'Rule added successfully to ' . $section->name);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroyRule(SectionRule $rule)
    {
        $blueprintId = $rule->blueprintSection->paper_blueprint_id;
        $rule->delete();

        return redirect()->route('admin.blueprints.show', $blueprintId)
                         ->with('success', 'Rule deleted successfully.');
    }
}
