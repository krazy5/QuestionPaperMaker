<?php

namespace App\Http\Controllers\Institute;

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
     * Display a listing of the institute's blueprints.
     */
    public function index()
    {
        // Fetch only blueprints created by the currently logged-in institute
        $blueprints = PaperBlueprint::where('institute_id', auth()->id())
            ->with('board', 'academicClass', 'subject')
            ->latest()
            ->paginate(15);

        return view('institute.blueprints.index', compact('blueprints'));
    }

    /**
     * Show the form for creating a new blueprint.
     */
    public function create()
    {
        $boards = Board::all();
        $classes = AcademicClassModel::all();
        
        return view('institute.blueprints.create', compact('boards', 'classes'));
    }

    /**
     * Store a newly created blueprint in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:academic_class_models,id',
            'subject_id' => 'required|exists:subjects,id',
            'total_marks' => 'required|integer|min:1',
            //'institute_id' => 'required|exists:users,id',
            'selected_chapters' => 'nullable|array', // Can be empty, but must be an array
        ]);

        // IMPORTANT: Associate the blueprint with the current institute
        $validated['institute_id'] = auth()->id();

        $blueprint = PaperBlueprint::create($validated);

        // Redirect to a page where they can add sections
        return redirect()->route('institute.blueprints.show', $blueprint)
                         ->with('success', 'Blueprint created successfully! Now you can add sections.');
    }

    /**
     * Display the specified blueprint.
     */
    
    public function show(PaperBlueprint $blueprint)
    {
        // Security check
        if ($blueprint->institute_id !== auth()->id()) {
            abort(403);
        }
        
        // ✅ Eager load the relationships to prevent performance issues
        $blueprint->load('sections.rules');

        return view('institute.blueprints.show', compact('blueprint'));
    }

    /**
     * Show the form for editing the specified blueprint.
     */
    
    public function edit(PaperBlueprint $blueprint)
    {
        // Security: Ensure an institute can only edit their own blueprints
        if ($blueprint->institute_id !== auth()->id()) {
            abort(403);
        }

        $boards = Board::all();
        $classes = AcademicClassModel::all();
        
        // Return the 'edit' view and pass the existing blueprint data to it
        return view('institute.blueprints.edit', compact('blueprint', 'boards', 'classes'));
    }

    /**
     * Update the specified blueprint in storage.
     */
    public function update(Request $request, PaperBlueprint $blueprint)
    {
        // Security: Ensure an institute can only update their own blueprints
        if ($blueprint->institute_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:academic_class_models,id',
            'subject_id' => 'required|exists:subjects,id',
            'total_marks' => 'required|integer|min:1',
            'selected_chapters' => 'nullable|array',
        ]);

        $blueprint->update($validated);

        return redirect()->route('institute.blueprints.index')->with('success', 'Blueprint updated successfully!');
    }

    

    /**
     * Remove the specified blueprint from storage.
     */
    public function destroy(PaperBlueprint $blueprint)
    {
        // Logic for deleting can be added later
    }



    public function storeSection(Request $request, PaperBlueprint $blueprint)
    {
        // Security check
        if ($blueprint->institute_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        $sortOrder = $blueprint->sections()->count() + 1;

        $blueprint->sections()->create([
            'name' => $validated['name'],
            'instructions' => $validated['instructions'],
            'sort_order' => $sortOrder,
        ]);

        return redirect()->route('institute.blueprints.show', $blueprint)
                         ->with('success', 'Section added successfully!');
    }

    public function storeRule(Request $request, BlueprintSection $section)
    {
        // Security check
        if ($section->paperBlueprint->institute_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_type' => 'required|in:mcq,short,long,true_false',
            'marks_per_question' => 'required|integer|min:1',
            'number_of_questions_to_select' => 'required|integer|min:1',
            'total_questions_to_display' => 'nullable|integer|gte:number_of_questions_to_select',
        ]);

        $section->rules()->create($validated);

        return redirect()->route('institute.blueprints.show', $section->paper_blueprint_id)
                         ->with('success', 'Rule added successfully to ' . $section->name);
    }
}