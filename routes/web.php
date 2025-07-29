<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// --- Controllers ---
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BoardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Institute\PaperController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public and Auth Routes ---

Route::get('/', function () {
    return view('welcome');
});

// This route intelligently redirects users to the correct dashboard after login
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->role === 'institute') {
        return redirect()->route('institute.dashboard');
    }
    // Default fallback
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Admin Routes ---
// All admin routes are now protected by the 'role:admin' middleware
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('boards', BoardController::class);
    Route::resource('classes', ClassController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('chapters', ChapterController::class);
    Route::resource('questions', QuestionController::class);
});

// --- Institute Routes ---
// All institute routes are now protected by the 'role:institute' middleware
Route::middleware(['auth', 'role:institute'])->prefix('institute')->name('institute.')->group(function () {
    Route::get('/dashboard', [PaperController::class, 'index'])->name('dashboard');
    Route::get('/papers/create', [PaperController::class, 'create'])->name('papers.create');
    Route::post('/papers', [PaperController::class, 'store'])->name('papers.store');
    Route::get('/papers/{paper}/select-questions', [PaperController::class, 'selectQuestions'])->name('papers.questions.select');
    Route::post('/papers/{paper}/save-questions', [PaperController::class, 'saveQuestions'])->name('papers.questions.save');
    Route::get('/papers/{paper}/preview', [PaperController::class, 'preview'])->name('papers.preview');
    Route::get('/papers/{paper}/edit', [PaperController::class, 'edit'])->name('papers.edit');
    Route::put('/papers/{paper}', [PaperController::class, 'update'])->name('papers.update');
    Route::delete('/papers/{paper}', [PaperController::class, 'destroy'])->name('papers.destroy');
    Route::get('/papers/{paper}/answers', [PaperController::class, 'previewAnswers'])->name('papers.answers');

});

require __DIR__.'/auth.php';
