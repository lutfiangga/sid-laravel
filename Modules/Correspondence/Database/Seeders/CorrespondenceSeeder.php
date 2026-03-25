<?php

declare(strict_types=1);

namespace Modules\Correspondence\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Correspondence\Models\LetterType;
use Modules\Correspondence\Models\LetterRequest;
use Modules\Population\Models\Penduduk;
use App\Models\User;

class CorrespondenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Letter Types
        $types = [
            [
                'nama' => 'Surat Keterangan Usaha',
                'kode' => 'SKU',
                'template' => 'Yang bertanda tangan di bawah ini menerangkan bahwa {nama} memiliki usaha...',
                'requirement_list' => ['Fotokopi KTP', 'Fotokopi KK', 'Surat Pengantar RT/RW'],
            ],
            [
                'nama' => 'Surat Keterangan Tidak Mampu',
                'kode' => 'SKTM',
                'template' => 'Diterangkan bahwa keluarga {nama} termasuk dalam kategori keluarga tidak mampu...',
                'requirement_list' => ['Fotokopi KK', 'Pernyataan bermaterai'],
            ],
            [
                'nama' => 'Surat Pengantar Domisili',
                'kode' => 'SPD',
                'template' => 'Menerangkan bahwa {nama} benar berdomisili di...',
                'requirement_list' => ['Surat pengantar RT'],
            ],
        ];

        foreach ($types as $type) {
            LetterType::updateOrCreate(['kode' => $type['kode']], $type);
        }

        // 2. Create Sample Requests if residents exist
        $penduduk = Penduduk::first();
        $letterType = LetterType::first();
        $user = User::first();

        if ($penduduk && $letterType && $user) {
            LetterRequest::create([
                'penduduk_id' => $penduduk->id,
                'letter_type_id' => $letterType->id,
                'data' => ['keperluan' => 'Persyaratan KUR Bank Mandiri'],
                'workflow_status' => 'submitted',
            ]);

            LetterRequest::create([
                'penduduk_id' => $penduduk->id,
                'letter_type_id' => $letterType->id,
                'data' => ['keperluan' => 'Pembukaan rekening bank'],
                'workflow_status' => 'approved',
                'nomor_surat' => '001/SKU/PEM-DS/2026',
            ]);
        }
    }
}
