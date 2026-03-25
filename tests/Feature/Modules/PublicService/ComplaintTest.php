<?php

use Modules\PublicService\Models\Complaint;
use Modules\PublicService\Services\ComplaintService;
use Modules\Population\Models\Penduduk;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a complaint via service', function () {
    $penduduk = Penduduk::factory()->create();
    $service = app(ComplaintService::class);
    
    $data = [
        'penduduk_id' => $penduduk->id,
        'title' => 'Jalan Rusak',
        'description' => 'Tolong diperbaiki',
        'status' => 'pending',
    ];
    
    $complaint = $service->create($data);
    
    expect($complaint)->toBeInstanceOf(Complaint::class)
        ->title->toBe('Jalan Rusak')
        ->status->toBe('pending');
        
    $this->assertDatabaseHas('complaints', [
        'title' => 'Jalan Rusak',
        'status' => 'pending',
    ]);
});

it('can update a complaint status via service', function () {
    $complaint = Complaint::factory()->create();
    $service = app(ComplaintService::class);
    
    $service->update($complaint->id, [
        'status' => 'resolved',
        'response' => 'Sudah diperbaiki kemarin',
    ]);
    
    $this->assertDatabaseHas('complaints', [
        'id' => $complaint->id,
        'status' => 'resolved',
        'response' => 'Sudah diperbaiki kemarin',
    ]);
});
