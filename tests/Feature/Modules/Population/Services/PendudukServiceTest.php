<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Population\Services;

use Modules\Population\Contracts\Services\PendudukServiceInterface;
use Modules\Population\Models\Penduduk;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('penduduk service prevents duplicate nik', function () {
    $penduduk = Penduduk::factory()->create(['nik' => '1234567890123456']);
    $service = app(PendudukServiceInterface::class);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('NIK 1234567890123456 sudah terdaftar.');

    $service->create([
        'nik' => '1234567890123456',
        'nama' => 'Duplicate Name',
        'kartu_keluarga_id' => $penduduk->kartu_keluarga_id,
        // ... more required fields but service should fail early in beforeCreate
    ]);
});
