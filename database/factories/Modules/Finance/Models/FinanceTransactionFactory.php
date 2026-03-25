<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Finance\Models\FinanceTransaction;
use Modules\Finance\Models\FinancePeriod;
use Modules\Finance\Models\FinanceAccount;

class FinanceTransactionFactory extends Factory
{
    protected $model = FinanceTransaction::class;

    public function definition(): array
    {
        return [
            'finance_period_id' => FinancePeriod::factory(),
            'finance_account_id' => FinanceAccount::factory(),
            'type' => $this->faker->randomElement(['pemasukan', 'pengeluaran']),
            'transaction_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 50000, 50000000),
            'description' => $this->faker->sentence(),
            'evidence_file' => null,
        ];
    }
}
