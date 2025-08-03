<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Paper;
use App\Models\SectionRule;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- THIS IS THE UPDATED ROUTE ---
    // It now passes the Request object to the controller method
    Route::get('/papers/{paperId}/questions-for-rule/{ruleId}', function (Request $request, $paperId, $ruleId) {
        return app(\App\Http\Controllers\Institute\PaperController::class)->getQuestionsForRule($request, $paperId, $ruleId);
    });

    Route::get('/papers/{paper}/stats', function (Paper $paper) {
        return app(\App\Http\Controllers\Institute\PaperController::class)->getPaperStats($paper);
    });

});

// Public routes
Route::get('/subjects-by-class', function (Request $request) {
    return Subject::where('class_id', $request->query('class_id'))->get();
});

Route::get('/chapters-by-subject', function (Request $request) {
    return Chapter::where('subject_id', $request->query('subject_id'))->get();
});
