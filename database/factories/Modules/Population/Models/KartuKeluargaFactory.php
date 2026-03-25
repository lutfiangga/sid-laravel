<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Population\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Population\Models\KartuKeluarga;
use Modules\Population\Models\Rt;

class KartuKeluargaFactory extends Factory
{
    protected $model = KartuKeluarga::class;

    public function definition(): array
    {
        return [
            'rt_id' => Rt::factory(),
            'nomor_kk' => $this->faker->unique()->numerify('################'),
            'kepala_keluarga' => $this->faker->name('male'),
            'alamat' => $this->faker->address(),
        ];
    }
}
