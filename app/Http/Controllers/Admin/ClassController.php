<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicClassModel;

use App\Models\Board; // 👈 NEW IMPORT
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * List classes with search, sort, and per-page controls.
     */
    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', '');
        $per  = (int) $request->query('per', 10);
        $per  = in_array($per, [10, 25, 50, 100], true) ? $per : 10;

        $query = AcademicClassModel::query();

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($w) use ($like, $q) {
                $w->where('name', 'like', $like);
                // Also allow searching by id (exact if numeric, else like)
                if (is_numeric($q)) {
                    $w->orWhere('id', (int) $q);
                } else {
                    $w->orWhere('id', 'like', $like);
                }
            });
        }

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderByDesc('id'); // newest first
        }

        $classes = $query->paginate($per)->appends($request->query());

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        // 💡 FETCH ALL BOARDS
        $boards = Board::orderBy('name')->get();
        return view('admin.classes.create', compact('boards')); // 👈 PASS BOARDS
    }

    public function store(Request $request)
    {
         $validated = $request->validate([
            // 💡 ADD board_id VALIDATION
            'board_id' => 'required|exists:boards,id',
            'name'     => 'required|string|max:255|unique:academic_class_models',
        ]);

        // 💡 INCLUDE board_id in creation
        AcademicClassModel::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully!');
    }

    public function edit(AcademicClassModel $class)
    {
        // 💡 FETCH ALL BOARDS
        $boards = Board::orderBy('name')->get();
        // 👈 PASS BOTH CLASS AND BOARDS
        return view('admin.classes.edit', ['class' => $class, 'boards' => $boards]);
    }

    public function update(Request $request, AcademicClassModel $class)
    {
        $validated = $request->validate([
            // 💡 ADD board_id VALIDATION
            'board_id' => 'required|exists:boards,id',
            'name'     => 'required|string|max:255|unique:academic_class_models,name,' . $class->id,
        ]);

        // 💡 INCLUDE board_id in update
        $class->update($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully!');
    }

    public function destroy(AcademicClassModel $class)
    {
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully!');
    }
}
