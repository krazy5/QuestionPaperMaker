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
use App\Http\Controllers\Admin\InstituteController;
use App\Http\Controllers\Institute\PaperController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\PaperBlueprintController;
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
     Route::get('/pricing', [SubscriptionController::class, 'index'])->name('subscription.pricing');
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
Route::post('/subscription/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');


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
    // ADD THIS NEW ROUTE
    Route::resource('institutes', InstituteController::class)->only(['index', 'show']);
    // ADD THIS NEW ROUTE for storing a manually created subscription
    Route::post('/institutes/{institute}/subscriptions', [InstituteController::class, 'storeSubscription'])->name('institutes.subscriptions.store');
     // ADD THIS NEW ROUTE for cancelling a specific subscription
    Route::post('/subscriptions/{subscription}/cancel', [InstituteController::class, 'cancelSubscription'])->name('institutes.subscriptions.cancel');
    
    Route::resource('blueprints', PaperBlueprintController::class);
    Route::post('/blueprints/{blueprint}/sections', [PaperBlueprintController::class, 'storeSection'])->name('blueprints.sections.store');
    Route::post('/blueprints/sections/{section}/rules', [PaperBlueprintController::class, 'storeRule'])->name('blueprints.sections.rules.store');
Route::delete('/blueprints/rules/{rule}', [PaperBlueprintController::class, 'destroyRule'])->name('blueprints.rules.destroy');

});

// --- Institute Routes ---
// All institute routes are now protected by the 'role:institute' middleware
Route::middleware(['auth', 'role:institute'])->prefix('institute')->name('institute.')->group(function () {
    Route::get('/dashboard', [PaperController::class, 'index'])->name('dashboard');

     // ADD THIS LINE FOR MANAGING QUESTIONS
    Route::resource('questions', \App\Http\Controllers\Institute\QuestionController::class);

    Route::get('/get-subjects-for-class/{classId}', [PaperController::class, 'getSubjectsForClass'])->name('subjects.for.class');


    // PROTECT THESE ROUTES
    Route::get('/papers/create', [PaperController::class, 'create'])->name('papers.create')->middleware('subscribed');
    Route::post('/papers', [PaperController::class, 'store'])->name('papers.store')->middleware('subscribed');
    

    Route::get('/papers/{paper}/select-questions', [PaperController::class, 'selectQuestions'])->name('papers.questions.select');
    Route::post('/papers/{paper}/save-questions', [PaperController::class, 'saveQuestions'])->name('papers.questions.save');
    Route::get('/papers/{paper}/preview', [PaperController::class, 'preview'])->name('papers.preview');
    Route::get('/papers/{paper}/edit', [PaperController::class, 'edit'])->name('papers.edit');
    Route::put('/papers/{paper}', [PaperController::class, 'update'])->name('papers.update');
    Route::delete('/papers/{paper}', [PaperController::class, 'destroy'])->name('papers.destroy');
    Route::get('/papers/{paper}/answers', [PaperController::class, 'previewAnswers'])->name('papers.answers');


    // ADD THESE TWO NEW ROUTES FOR INSTANT SAVING
    Route::post('/papers/{paper}/questions/{question}/attach', [PaperController::class, 'attachQuestion'])->name('papers.questions.attach');
    Route::post('/papers/{paper}/questions/{question}/detach', [PaperController::class, 'detachQuestion'])->name('papers.questions.detach');
     
    Route::get('/papers/{paper}/fulfill-blueprint', [PaperController::class, 'fulfillBlueprint'])->name('papers.fulfill_blueprint');
Route::post('/papers/{paper}/auto-fill', [PaperController::class, 'autoFillBlueprint'])->name('papers.auto_fill');

});

require __DIR__.'/auth.php';
