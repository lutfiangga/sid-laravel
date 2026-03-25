<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Population\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Population\Models\Dusun;
use Modules\Population\Models\Rw;

class RwFactory extends Factory
{
    protected $model = Rw::class;

    public function definition(): array
    {
        return [
            'dusun_id' => Dusun::factory(),
            'nomor' => $this->faker->bothify('0##'),
            'ketua' => $this->faker->name(),
        ];
    }
}
