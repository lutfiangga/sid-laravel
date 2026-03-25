<?php

declare(strict_types=1);

namespace Database\Factories\Modules\PublicService\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\PublicService\Models\Apparatus;

class ApparatusFactory extends Factory
{
    protected $model = Apparatus::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->jobTitle(),
            'nip' => $this->faker->numerify('##################'), // 18 digit NIP typical in ID
            'status' => 'aktif',
        ];
    }
}
