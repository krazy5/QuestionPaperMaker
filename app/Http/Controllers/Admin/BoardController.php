<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board; // <-- Import the Board model


class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
         // Fetch all boards from the database
        $boards = Board::latest()->get();

        // Return a view and pass the boards data to it
        return view('admin.boards.index', ['boards' => $boards]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //// This just needs to return the view with the form.
        return view('admin.boards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // 1. Validate the incoming data
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:boards',
            ]);

            // 2. Create the new board using the validated data
            Board::create($validated);

            // 3. Redirect back to the index page with a success message
            return redirect()->route('boards.index')->with('success', 'Board created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Board $board)
    {
        //
        return view('admin.boards.edit', ['board' => $board]);
    }

    /**
     * Update the specified resource in storage.
     */
   /**
 * Update the specified resource in storage.
 */
        public function update(Request $request, Board $board)
        {
            // 1. Validate the incoming data
            $validated = $request->validate([
                // The unique rule must ignore the current board's ID
                'name' => 'required|string|max:255|unique:boards,name,' . $board->id,
            ]);

            // 2. Update the board with the validated data
            $board->update($validated);

            // 3. Redirect back to the index page with a success message
            return redirect()->route('admin.boards.index')->with('success', 'Board updated successfully!');
        }

    /**
     * Remove the specified resource from storage.
     */
    /**
 * Remove the specified resource from storage.
 */
        public function destroy(Board $board)
        {
            // 1. Delete the board
            $board->delete();

            // 2. Redirect back with a success message
            return redirect()->route('admin.boards.index')->with('success', 'Board deleted successfully!');
        }
}
