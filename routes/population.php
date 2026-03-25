<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('population')->name('population.')->group(function () {
    Route::redirect('/', '/population/penduduk');

    // Dusun
    Route::livewire('/dusun', 'pages::population.dusun.index')->name('dusun.index');
    Route::livewire('/dusun/create', 'pages::population.dusun.form')->name('dusun.create');
    Route::livewire('/dusun/{dusun}/edit', 'pages::population.dusun.form')->name('dusun.edit');

    // RW
    Route::livewire('/rw', 'pages::population.rw.index')->name('rw.index');
    Route::livewire('/rw/create', 'pages::population.rw.form')->name('rw.create');
    Route::livewire('/rw/{rw}/edit', 'pages::population.rw.form')->name('rw.edit');

    // RT
    Route::livewire('/rt', 'pages::population.rt.index')->name('rt.index');
    Route::livewire('/rt/create', 'pages::population.rt.form')->name('rt.create');
    Route::livewire('/rt/{rt}/edit', 'pages::population.rt.form')->name('rt.edit');

    // Kartu Keluarga
    Route::livewire('/kartu-keluarga', 'pages::population.kartu-keluarga.index')->name('kartu-keluarga.index');
    Route::livewire('/kartu-keluarga/create', 'pages::population.kartu-keluarga.form')->name('kartu-keluarga.create');
    Route::livewire('/kartu-keluarga/{kartu_keluarga}/edit', 'pages::population.kartu-keluarga.form')->name('kartu-keluarga.edit');

    // Penduduk
    Route::livewire('/penduduk', 'pages::population.penduduk.index')->name('penduduk.index');
    Route::livewire('/penduduk/create', 'pages::population.penduduk.form')->name('penduduk.create');
    Route::livewire('/penduduk/{penduduk}/edit', 'pages::population.penduduk.form')->name('penduduk.edit');
});
