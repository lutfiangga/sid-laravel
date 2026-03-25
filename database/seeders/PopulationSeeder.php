<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Population\Models\Dusun;
use Modules\Population\Models\Rw;
use Modules\Population\Models\Rt;
use Modules\Population\Models\KartuKeluarga;
use Modules\Population\Models\Penduduk;

class PopulationSeeder extends Seeder
{
    public function run(): void
    {
        // Create 2 Dusun
        Dusun::factory()->count(2)->create()->each(function ($dusun) {
            // Create 2 RW for each Dusun
            Rw::factory()->count(2)->create(['dusun_id' => $dusun->id])->each(function ($rw) {
                // Create 2 RT for each RW
                Rt::factory()->count(2)->create(['rw_id' => $rw->id])->each(function ($rt) {
                    // Create 5 KK for each RT
                    KartuKeluarga::factory()->count(5)->create(['rt_id' => $rt->id])->each(function ($kk) {
                        // Create Penduduk for each KK
                        // One head of family
                        Penduduk::factory()->create([
                            'kartu_keluarga_id' => $kk->id,
                            'nama' => $kk->kepala_keluarga,
                            'jenis_kelamin' => 'L',
                            'status_dalam_keluarga' => 'Kepala Keluarga',
                        ]);

                        // 2-3 other members
                        Penduduk::factory()->count(rand(2, 3))->create([
                            'kartu_keluarga_id' => $kk->id,
                        ]);
                    });
                });
            });
        });
    }
}
