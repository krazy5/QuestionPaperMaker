<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Subject;
use App\Models\Chapter;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Add this route to get subjects based on a class ID
Route::get('/subjects-by-class', function (Request $request) {
    $classId = $request->query('class_id');
    if (!$classId) {
        return response()->json([]);
    }
    $subjects = Subject::where('class_id', $classId)->get();
    return response()->json($subjects);
});

// Route to get chapters based on a subject_id
Route::get('/chapters-by-subject', function (Request $request) {
    return Chapter::where('subject_id', $request->query('subject_id'))->get();
});