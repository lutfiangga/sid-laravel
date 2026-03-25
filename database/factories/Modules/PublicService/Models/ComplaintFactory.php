<?php

declare(strict_types=1);

namespace Database\Factories\Modules\PublicService\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\PublicService\Models\Complaint;
use Modules\Population\Models\Penduduk;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'penduduk_id' => Penduduk::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => 'pending',
            'response' => null,
        ];
    }
}
