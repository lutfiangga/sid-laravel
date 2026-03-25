<?php

namespace Database\Factories\Modules\Correspondence\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Correspondence\Models\LetterType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Correspondence\Models\LetterType>
 */
class LetterTypeFactory extends Factory
{
    protected $model = LetterType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'nama' => $this->faker->words(3, true),
            'kode' => strtoupper($this->faker->unique()->lexify('???')),
            'template' => $this->faker->paragraph(),
            'requirement_list' => ['KTP', 'KK'],
        ];
    }
}
