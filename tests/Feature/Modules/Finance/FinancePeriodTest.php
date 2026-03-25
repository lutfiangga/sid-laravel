<?php

use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Services\FinancePeriodService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a finance period and ensure only one is active', function () {
    $service = app(FinancePeriodService::class);
    
    // Create first active period
    $period1 = $service->create([
        'year' => 2025,
        'description' => 'Tahun 2025',
        'is_active' => true,
    ]);
    
    // Create second active period
    $period2 = $service->create([
        'year' => 2026,
        'description' => 'Tahun 2026',
        'is_active' => true,
    ]);
    
    $this->assertDatabaseHas('finance_periods', [
        'id' => $period2->id,
        'is_active' => 1, // DB cast
    ]);
    
    $this->assertDatabaseHas('finance_periods', [
        'id' => $period1->id,
        'is_active' => 0, // Should be deactivated
    ]);
});
