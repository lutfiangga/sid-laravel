<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Finance\Models\FinanceBudget;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Models\FinanceAccount;

class FinanceBudgetFactory extends Factory
{
    protected $model = FinanceBudget::class;

    public function definition(): array
    {
        return [
            'finance_period_id' => FinancePeriod::factory(),
            'finance_account_id' => FinanceAccount::factory(),
            'amount' => $this->faker->randomFloat(2, 1000000, 1000000000),
            'notes' => $this->faker->sentence(),
        ];
    }
}
