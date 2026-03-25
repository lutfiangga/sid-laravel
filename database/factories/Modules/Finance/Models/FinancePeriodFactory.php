<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Finance\Models\FinancePeriod;

class FinancePeriodFactory extends Factory
{
    protected $model = FinancePeriod::class;

    public function definition(): array
    {
        return [
            'year' => $this->faker->unique()->numberBetween(2020, 2030),
            'description' => 'APBD ' . $this->faker->year(),
            'is_active' => false,
        ];
    }
}
