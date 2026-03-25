<?php

use Modules\PublicService\Models\Apparatus;
use Modules\PublicService\Services\ApparatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create an apparatus via service', function () {
    $service = app(ApparatusService::class);
    
    $data = [
        'nama' => 'Budi Santoso',
        'jabatan' => 'Kepala Desa',
        'nip' => '123456789012345678',
        'status' => 'aktif',
    ];
    
    $apparatus = $service->create($data);
    
    expect($apparatus)->toBeInstanceOf(Apparatus::class)
        ->nama->toBe('Budi Santoso')
        ->jabatan->toBe('Kepala Desa');
        
    $this->assertDatabaseHas('apparatus', [
        'nama' => 'Budi Santoso',
        'status' => 'aktif',
    ]);
});

it('can paginate apparatus via service', function () {
    Apparatus::factory()->count(15)->create();
    
    $service = app(ApparatusService::class);
    $paginated = $service->getPaginated(perPage: 10);
    
    expect($paginated->count())->toBe(10)
        ->and($paginated->total())->toBe(15);
});
