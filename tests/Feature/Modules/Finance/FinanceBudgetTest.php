<?php

use Modules\Finance\Models\FinanceBudget;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Models\FinanceAccount;
use Modules\Finance\Services\FinanceBudgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a finance budget', function () {
    $period = FinancePeriod::factory()->create();
    $account = FinanceAccount::factory()->create();
    $service = app(FinanceBudgetService::class);
    
    $budget = $service->create([
        'finance_period_id' => $period->id,
        'finance_account_id' => $account->id,
        'amount' => 50000000,
        'notes' => 'Dana Desa',
    ]);
    
    expect($budget)->toBeInstanceOf(FinanceBudget::class);
        
    $this->assertDatabaseHas('finance_budgets', [
        'finance_period_id' => $period->id,
        'finance_account_id' => $account->id,
        'notes' => 'Dana Desa',
    ]);
});
