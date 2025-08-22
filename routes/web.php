<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Admin controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BoardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\InstituteController;
use App\Http\Controllers\Admin\PaperBlueprintController as AdminBlueprintController;

// Institute controllers
use App\Http\Controllers\Institute\PaperController;
use App\Http\Controllers\Institute\QuestionController as InstituteQuestionController;
use App\Http\Controllers\Institute\PaperBlueprintController as InstituteBlueprintController;

// Subscriptions
use App\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public
Route::get('/', function () {
    return view('welcome');
});

// Role-aware dashboard redirect
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->role === 'institute') {
        return redirect()->route('institute.dashboard');
    }
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated (common) routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Subscription (self-serve)
    Route::get('/pricing', [SubscriptionController::class, 'index'])->name('subscription.pricing');
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscription/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('boards', BoardController::class);
        Route::resource('classes', ClassController::class);
        Route::resource('subjects', SubjectController::class);
        Route::resource('chapters', ChapterController::class);
        Route::resource('questions', AdminQuestionController::class);

        // Institutes & subscriptions (admin-side)
        Route::resource('institutes', InstituteController::class)->only(['index', 'show']);
        Route::post('/institutes/{institute}/subscriptions', [InstituteController::class, 'storeSubscription'])
            ->name('institutes.subscriptions.store');
        Route::post('/subscriptions/{subscription}/cancel', [InstituteController::class, 'cancelSubscription'])
            ->name('institutes.subscriptions.cancel');

        // Admin: Blueprints
        Route::resource('blueprints', AdminBlueprintController::class);
        Route::post('/blueprints/{blueprint}/sections', [AdminBlueprintController::class, 'storeSection'])
            ->name('blueprints.sections.store');
        Route::post('/blueprints/sections/{section}/rules', [AdminBlueprintController::class, 'storeRule'])
            ->name('blueprints.sections.rules.store');
        Route::delete('/blueprints/rules/{rule}', [AdminBlueprintController::class, 'destroyRule'])
            ->name('blueprints.rules.destroy');
    });

/*
|--------------------------------------------------------------------------
| Institute Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:institute'])
    ->prefix('institute')
    ->name('institute.')
    ->group(function () {
        // Dashboard shows papers list + subscription panel
        Route::get('/dashboard', [PaperController::class, 'index'])->name('dashboard');

        // Institute questions module
        Route::resource('questions', InstituteQuestionController::class);

        // Helper: subjects for a class (AJAX)
        Route::get('/get-subjects-for-class/{classId}', [PaperController::class, 'getSubjectsForClass'])
            ->name('subjects.for.class');

        // Papers: creation is subscription-gated
        Route::get('/papers/create', [PaperController::class, 'create'])
            ->name('papers.create')
            ->middleware('subscribed');
        Route::post('/papers', [PaperController::class, 'store'])
            ->name('papers.store')
            ->middleware('subscribed');

        // Papers: remaining resource routes (index, show, edit, update, destroy)
        // Keep index so route('institute.papers.index') exists alongside the dashboard.
        Route::resource('papers', PaperController::class)->except(['create', 'store']);

        // Papers: extras
        Route::get('/papers/{paper}/preview', [PaperController::class, 'preview'])
            ->name('papers.preview');
        Route::get('/papers/{paper}/answers', [PaperController::class, 'previewAnswers'])
            ->name('papers.answers');

        // Manual question selection flow
        Route::get('/papers/{paper}/select-questions', [PaperController::class, 'selectQuestions'])
            ->name('papers.questions.select');
        Route::post('/papers/{paper}/save-questions', [PaperController::class, 'saveQuestions'])
            ->name('papers.questions.save');

        // Instant attach/detach APIs
        Route::post('/papers/{paper}/questions/attach', [PaperController::class, 'attachQuestion'])
            ->name('papers.questions.attach');
        Route::post('/papers/{paper}/questions/detach', [PaperController::class, 'detachQuestion'])
            ->name('papers.questions.detach');

        // Fulfill paper from blueprint
        Route::get('/papers/{paper}/fulfill-blueprint', [PaperController::class, 'fulfillBlueprint'])
            ->name('papers.fulfill_blueprint');
        Route::post('/papers/{paper}/auto-fill', [PaperController::class, 'autoFillBlueprint'])
            ->name('papers.auto_fill');

        // Institute: Blueprints
        Route::resource('blueprints', InstituteBlueprintController::class);
        Route::post('/blueprints/{blueprint}/sections', [InstituteBlueprintController::class, 'storeSection'])
            ->name('blueprints.sections.store');
        Route::post('/blueprints/sections/{section}/rules', [InstituteBlueprintController::class, 'storeRule'])
            ->name('blueprints.sections.rules.store');
        Route::delete('/blueprints/sections/{section}', [InstituteBlueprintController::class, 'destroySection'])
            ->name('blueprints.sections.destroy');
        Route::delete('/blueprints/sections/rules/{rule}', [InstituteBlueprintController::class, 'destroyRule'])
            ->name('blueprints.sections.rules.destroy');

        // Create a paper from a blueprint
        Route::post('/papers/create-from-blueprint/{blueprint}', [PaperController::class, 'createFromBlueprint'])
            ->name('papers.createFromBlueprint');



            // Blueprint decision page after creating a paper
        Route::get('/papers/{paper}/choose-blueprint', [PaperController::class, 'chooseBlueprint'])
            ->name('papers.choose_blueprint');

        // Attach a detected blueprint to a paper
        Route::post('/papers/{paper}/adopt-blueprint/{blueprint}', [PaperController::class, 'adoptBlueprint'])
            ->name('papers.adopt_blueprint');


             Route::prefix('api')->group(function () {
            Route::get('/papers/{paper}/questions-for-rule/{rule}', [\App\Http\Controllers\Institute\PaperController::class, 'apiQuestionsForRule'])
                ->name('papers.api.questions_for_rule');

            Route::get('/papers/{paper}/stats', [\App\Http\Controllers\Institute\PaperController::class, 'apiPaperRuleStats'])
                ->name('papers.api.stats');
        });

    });

require __DIR__ . '/auth.php';
