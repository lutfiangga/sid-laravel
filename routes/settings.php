<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    Route::livewire('settings/security', 'pages::settings.security')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('security.edit');

    Route::livewire('settings/users', 'pages::settings.users.index')
        ->name('users.index')
        ->middleware('can:viewAny,App\Models\User');

    Route::livewire('settings/users/create', 'pages::settings.users.form')
        ->name('users.create')
        ->middleware('can:create,App\Models\User');

    Route::livewire('settings/users/{user}/edit', 'pages::settings.users.form')
        ->name('users.edit')
        ->middleware('can:update,App\Models\User');

    Route::livewire('settings/rbac', 'pages::settings.rbac.index')
        ->name('rbac.index')
        ->middleware('can:viewAny,Spatie\Permission\Models\Role');

    Route::livewire('settings/rbac/create', 'pages::settings.rbac.form')
        ->name('rbac.create')
        ->middleware('can:create,Spatie\Permission\Models\Role');

    Route::livewire('settings/rbac/{role}/edit', 'pages::settings.rbac.form')
        ->name('rbac.edit')
        ->middleware('can:update,Spatie\Permission\Models\Role');
});
