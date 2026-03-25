<?php

use Modules\Finance\Models\FinanceAccount;
use Modules\Finance\Services\FinanceAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a finance account', function () {
    $service = app(FinanceAccountService::class);
    
    $account = $service->create([
        'code' => '4.1.1.01',
        'name' => 'Hasil Usaha Desa',
        'type' => 'pemasukan',
        'is_active' => true,
    ]);
    
    expect($account)->toBeInstanceOf(FinanceAccount::class)
        ->code->toBe('4.1.1.01')
        ->type->toBe('pemasukan');
        
    $this->assertDatabaseHas('finance_accounts', [
        'code' => '4.1.1.01',
        'type' => 'pemasukan',
    ]);
});
