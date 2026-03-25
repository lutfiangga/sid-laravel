<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Population\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Population\Models\KartuKeluarga;
use Modules\Population\Models\Penduduk;

class PendudukFactory extends Factory
{
    protected $model = Penduduk::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['L', 'P']);
        
        return [
            'kartu_keluarga_id' => KartuKeluarga::factory(),
            'nik' => $this->faker->unique()->numerify('################'),
            'nama' => $this->faker->name($gender === 'L' ? 'male' : 'female'),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->date(),
            'jenis_kelamin' => $gender,
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu']),
            'status_perkawinan' => $this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            'pekerjaan' => $this->faker->jobTitle(),
            'pendidikan_terakhir' => $this->faker->randomElement(['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'Diploma', 'S1', 'S2', 'S3']),
            'golongan_darah' => $this->faker->randomElement(['A', 'B', 'AB', 'O']),
            'status_dalam_keluarga' => $this->faker->randomElement(['Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya']),
            'kewarganegaraan' => 'WNI',
            'telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'status' => 'Aktif',
        ];
    }
}
