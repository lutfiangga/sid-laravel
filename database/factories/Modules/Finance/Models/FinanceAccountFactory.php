<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Finance\Models\FinanceAccount;

class FinanceAccountFactory extends Factory
{
    protected $model = FinanceAccount::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('#.#.#.##'),
            'name' => 'Akun ' . $this->faker->word(),
            'type' => $this->faker->randomElement(['pemasukan', 'pengeluaran', 'pembiayaan']),
            'is_active' => true,
        ];
    }
}
