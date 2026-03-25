<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::livewire('letter-templates', 'pages::letter-template.index')->name('letter-template.index');
    Route::livewire('letter-templates/create', 'pages::letter-template.form')->name('letter-template.create');
    Route::livewire('letter-templates/{letterTemplate}/edit', 'pages::letter-template.form')->name('letter-template.edit');
});
