<?php

use Modules\Finance\Models\FinanceTransaction;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Models\FinanceAccount;
use Modules\Finance\Services\FinanceTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can record a finance transaction', function () {
    $period = FinancePeriod::factory()->create();
    $account = FinanceAccount::factory()->create();
    $service = app(FinanceTransactionService::class);
    
    $transaction = $service->create([
        'finance_period_id' => $period->id,
        'finance_account_id' => $account->id,
        'type' => 'pengeluaran',
        'transaction_date' => '2026-05-10',
        'amount' => 1500000,
        'description' => 'Beli ATK Desa',
    ]);
    
    expect($transaction)->toBeInstanceOf(FinanceTransaction::class)
        ->type->toBe('pengeluaran');
        
    $this->assertDatabaseHas('finance_transactions', [
        'finance_period_id' => $period->id,
        'finance_account_id' => $account->id,
        'type' => 'pengeluaran',
        'description' => 'Beli ATK Desa',
    ]);
});
