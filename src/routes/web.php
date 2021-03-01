<?php

use Illuminate\Support\Facades\Route;
use KillerQuote\App\Controllers\QuotesController;
use KillerQuote\App\Controllers\KillerQuotesController;
use KillerQuote\App\Controllers\SettingsController;
use KillerQuote\App\Controllers\SettingsLocaleController;
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
        Route::post('settings/upload_pdf', [SettingsController::class, 'uploadPdf']);
        Route::post('settings/upload_firma', [SettingsController::class, 'uploadFirma']);
        Route::post('settings/upload_review_image', [SettingsController::class, 'uploadReviewImg']);
        Route::post('settings/delete_review_image/{id}', [SettingsController::class, 'deleteReviewImg']);

        //Settings locale
        Route::get('settings/{locale}', [SettingsLocaleController::class, 'index']);
        Route::post('settings/{locale}', [SettingsLocaleController::class, 'update']);
        Route::post('settings/upload_logo/{locale}', [SettingsLocaleController::class, 'uploadLogo']);
        Route::post('settings/upload_pdf/{locale}', [SettingsLocaleController::class, 'uploadPdf']);
        Route::post('settings/upload_firma/{locale}', [SettingsLocaleController::class, 'uploadFirma']);

    });

    // Export PDF
    Route::get('killerquotes/{id}/pdf', [KillerQuotesController::class, 'pdf']);
    // Export PDF locale
    Route::get('killerquotes/{id}/pdf/{locale}', [KillerQuotesController::class, 'pdfLocale']);

    Route::post('killerquotes/{id}/send-pdf', [KillerQuotesController::class, 'sendPdf'])->name('killequotes.sendPdf');

    // Resource
    Route::post('killerquotes/{quote}/duplicate', [KillerQuotesController::class, 'duplicate'])->name('killerquotes.duplicate');
    Route::resource('killerquotes', KillerQuotesController::class);

    Route::resource('quotes', QuotesController::class)->except('index');

    Route::post('contacts/make-company-and-quote', [KillerQuotesController::class, 'makeCompanyAndQuote'])->name('killerquotes.makeCompanyAndQuote');

});
