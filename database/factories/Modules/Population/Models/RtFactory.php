<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Population\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Population\Models\Rt;
use Modules\Population\Models\Rw;

class RtFactory extends Factory
{
    protected $model = Rt::class;

    public function definition(): array
    {
        return [
            'rw_id' => Rw::factory(),
            'nomor' => $this->faker->bothify('0##'),
            'ketua' => $this->faker->name(),
        ];
    }
}
