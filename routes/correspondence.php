<?php

use Illuminate\Support\Facades\Route;
use Modules\Correspondence\Http\Controllers\LetterPdfController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Letter Types (Admin)
    Route::livewire('/correspondence/letter-types', 'pages::correspondence.letter-type.index')->name('correspondence.letter-type.index');
    Route::livewire('/correspondence/letter-types/create', 'pages::correspondence.letter-type.form')->name('correspondence.letter-type.create');
    Route::livewire('/correspondence/letter-types/{letterType}/edit', 'pages::correspondence.letter-type.form')->name('correspondence.letter-type.edit');

    // Letter Requests (Resident & Official)
    Route::livewire('/correspondence/requests', 'pages::correspondence.letter-request.index')->name('correspondence.letter-request.index');
    Route::livewire('/correspondence/requests/create', 'pages::correspondence.letter-request.form')->name('correspondence.letter-request.create');
    Route::livewire('/correspondence/requests/{letterRequest}', 'pages::correspondence.letter-request.detail')->name('correspondence.letter-request.detail');

    // Approval Inbox
    Route::livewire('/correspondence/approval', 'pages::correspondence.approval.index')->name('correspondence.approval.index');

    // PDF Download (only for approved letters)
    Route::get('/correspondence/requests/{letterRequest}/download', LetterPdfController::class)->name('correspondence.letter-request.download');
});
