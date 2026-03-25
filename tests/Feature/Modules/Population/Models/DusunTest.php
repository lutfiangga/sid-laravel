<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Population\Models;

use Modules\Population\Models\Dusun;
use Modules\Population\Models\Rw;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dusun can be created', function () {
    $dusun = Dusun::factory()->create([
        'nama' => 'Dusun Krajan',
        'kode' => 'KRAJ01',
    ]);

    expect($dusun->nama)->toBe('Dusun Krajan')
        ->and($dusun->kode)->toBe('KRAJ01');
});

test('dusun has many rws', function () {
    $dusun = Dusun::factory()->create();
    Rw::factory()->count(3)->create(['dusun_id' => $dusun->id]);

    expect($dusun->rws)->toHaveCount(3);
});
