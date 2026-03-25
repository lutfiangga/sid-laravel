<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('finance')->name('finance.')->group(function () {
    // Periods
    Route::livewire('/periods', 'pages::finance.periods.index')
        ->name('periods.index')
        ->middleware('can:viewAny,Modules\Finance\Models\FinancePeriod');
        
    Route::livewire('/periods/create', 'pages::finance.periods.form')
        ->name('periods.create')
        ->middleware('can:create,Modules\Finance\Models\FinancePeriod');

    Route::livewire('/periods/{period}/edit', 'pages::finance.periods.form')
        ->name('periods.edit')
        ->middleware('can:update,period');

    // Accounts
    Route::livewire('/accounts', 'pages::finance.accounts.index')
        ->name('accounts.index')
        ->middleware('can:viewAny,Modules\Finance\Models\FinanceAccount');
        
    Route::livewire('/accounts/create', 'pages::finance.accounts.form')
        ->name('accounts.create')
        ->middleware('can:create,Modules\Finance\Models\FinanceAccount');

    Route::livewire('/accounts/{account}/edit', 'pages::finance.accounts.form')
        ->name('accounts.edit')
        ->middleware('can:update,account');

    // Budgets
    Route::livewire('/budgets', 'pages::finance.budgets.index')
        ->name('budgets.index')
        ->middleware('can:viewAny,Modules\Finance\Models\FinanceBudget');
        
    Route::livewire('/budgets/create', 'pages::finance.budgets.form')
        ->name('budgets.create')
        ->middleware('can:create,Modules\Finance\Models\FinanceBudget');

    Route::livewire('/budgets/{budget}/edit', 'pages::finance.budgets.form')
        ->name('budgets.edit')
        ->middleware('can:update,budget');

    // Transactions
    Route::livewire('/transactions', 'pages::finance.transactions.index')
        ->name('transactions.index')
        ->middleware('can:viewAny,Modules\Finance\Models\FinanceTransaction');
        
    Route::livewire('/transactions/create', 'pages::finance.transactions.form')
        ->name('transactions.create')
        ->middleware('can:create,Modules\Finance\Models\FinanceTransaction');

    Route::livewire('/transactions/{transaction}/edit', 'pages::finance.transactions.form')
        ->name('transactions.edit')
        ->middleware('can:update,transaction');
});
