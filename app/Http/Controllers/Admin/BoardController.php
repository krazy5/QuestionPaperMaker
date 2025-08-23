<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board;

class BoardController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', '');
        $per  = (int) $request->query('per', 10);
        $per  = in_array($per, [10, 25, 50, 100], true) ? $per : 10;

        $query = Board::query();

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($w) use ($like, $q) {
                $w->where('name', 'like', $like);
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
                $query->orderByDesc('id');
        }

        $boards = $query->paginate($per)->appends($request->query());

        return view('admin.boards.index', compact('boards'));
    }

    public function create()
    {
        return view('admin.boards.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // adjust table name if yours differs
            'name' => 'required|string|max:255|unique:boards,name',
        ]);

        Board::create($validated);

        return redirect()->route('admin.boards.index')
            ->with('success', 'Board created successfully!');
    }

    public function edit(Board $board)
    {
        return view('admin.boards.edit', compact('board'));
    }

    public function update(Request $request, Board $board)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:boards,name,' . $board->id,
        ]);

        $board->update($validated);

        return redirect()->route('admin.boards.index')
            ->with('success', 'Board updated successfully!');
    }

    public function destroy(Board $board)
    {
        $board->delete();

        return redirect()->route('admin.boards.index')
            ->with('success', 'Board deleted successfully!');
    }
}
