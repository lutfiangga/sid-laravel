<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('public-service')->name('public-service.')->group(function () {
    // Apparatus Routes
    Route::livewire('/apparatus', 'pages::public-service.apparatus.index')
        ->name('apparatus.index')
        ->middleware('can:viewAny,Modules\PublicService\Models\Apparatus');
        
    Route::livewire('/apparatus/create', 'pages::public-service.apparatus.form')
        ->name('apparatus.create')
        ->middleware('can:create,Modules\PublicService\Models\Apparatus');

    Route::livewire('/apparatus/{apparatus}/edit', 'pages::public-service.apparatus.form')
        ->name('apparatus.edit')
        ->middleware('can:update,apparatus');

    // Announcement Routes
    Route::livewire('/announcements', 'pages::public-service.announcements.index')
        ->name('announcements.index')
        ->middleware('can:viewAny,Modules\PublicService\Models\Announcement');
        
    Route::livewire('/announcements/create', 'pages::public-service.announcements.form')
        ->name('announcements.create')
        ->middleware('can:create,Modules\PublicService\Models\Announcement');

    Route::livewire('/announcements/{announcement}/edit', 'pages::public-service.announcements.form')
        ->name('announcements.edit')
        ->middleware('can:update,announcement');

    // Complaint Routes
    Route::livewire('/complaints', 'pages::public-service.complaints.index')
        ->name('complaints.index')
        ->middleware('can:viewAny,Modules\PublicService\Models\Complaint');
        
    Route::livewire('/complaints/create', 'pages::public-service.complaints.form')
        ->name('complaints.create')
        ->middleware('can:create,Modules\PublicService\Models\Complaint');

    Route::livewire('/complaints/{complaint}', 'pages::public-service.complaints.detail')
        ->name('complaints.detail')
        ->middleware('can:view,complaint');
});
