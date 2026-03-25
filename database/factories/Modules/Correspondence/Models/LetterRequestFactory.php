<?php

namespace Database\Factories\Modules\Correspondence\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Correspondence\Models\LetterRequest;
use Modules\Correspondence\Models\LetterType;
use Modules\Population\Models\Penduduk;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Correspondence\Models\LetterRequest>
 */
class LetterRequestFactory extends Factory
{
    protected $model = LetterRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'penduduk_id' => Penduduk::factory(),
            'letter_type_id' => LetterType::factory(),
            'nomor_surat' => null,
            'data' => ['keperluan' => $this->faker->sentence()],
            'workflow_status' => 'draft',
        ];
    }
}
