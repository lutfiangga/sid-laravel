<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Population\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Population\Models\Dusun;

class DusunFactory extends Factory
{
    protected $model = Dusun::class;

    public function definition(): array
    {
        return [
            'nama' => 'Dusun ' . $this->faker->words(2, true),
            'kode' => $this->faker->unique()->bothify('DSN##'),
            'ketua' => $this->faker->name(),
        ];
    }
}
