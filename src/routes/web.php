<?php

use Illuminate\Support\Facades\Route;
use KillerQuote\App\Controllers\KillerQuotesController;
use KillerQuote\App\Controllers\SettingsController;
use KillerQuote\App\Controllers\SummernoteController;
use KillerQuote\App\Controllers\PdfController;

// Killer Quotes Routes
Route::group(['middleware' => ['web', 'auth']], function() {

    // Killer Quotes
    Route::group(['prefix' => 'killerquotes'], function() {
        // Summernote
        Route::group(['prefix' => 'summernote'], function() {
            Route::post('upload_image', [SummernoteController::class, 'uploadImage']);
        });

        // Settings
        Route::get('settings', [SettingsController::class, 'index']);
        Route::post('settings', [SettingsController::class, 'update']);
        Route::post('settings/upload_logo', [SettingsController::class, 'uploadLogo']);
        Route::post('settings/upload_review_image', [SettingsController::class, 'uploadReviewImg']);
        Route::post('settings/delete_review_image/{id}', [SettingsController::class, 'deleteReviewImg']);
    });

    // Export PDF
    Route::get('killerquotes/{id}/pdf', [KillerQuotesController::class, 'pdf']);

    // Resource
    Route::post('killerquotes/{quote}/duplicate', [KillerQuotesController::class, 'duplicate'])->name('killerquotes.duplicate');
    Route::resource('killerquotes', KillerQuotesController::class);
});
