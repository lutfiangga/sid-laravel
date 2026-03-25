<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Correspondence\Services;

use Modules\Correspondence\Models\LetterType;
use Modules\Correspondence\Models\LetterRequest;
use Modules\Correspondence\Services\LetterRequestService;
use Modules\Population\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->penduduk = Penduduk::factory()->create();
    $this->type = LetterType::factory()->create(['kode' => 'SKU']);
});

test('letter request service can create request', function () {
    $service = app(LetterRequestService::class);
    $request = $service->create([
        'penduduk_id' => $this->penduduk->id,
        'letter_type_id' => $this->type->id,
        'data' => ['keperluan' => 'Test'],
    ]);

    expect($request->workflow_status)->toBe('draft');
});

test('letter request service can submit request', function () {
    $service = app(LetterRequestService::class);
    $request = LetterRequest::factory()->create([
        'workflow_status' => 'draft',
    ]);

    $success = $service->submitRequest($request->id);

    expect($success)->toBeTrue();
    expect($request->fresh()->workflow_status)->toBe('submitted');
});

test('letter request service handles approval workflow', function () {
    $service = app(LetterRequestService::class);
    $request = LetterRequest::factory()->create([
        'letter_type_id' => $this->type->id,
        'workflow_status' => 'submitted',
    ]);

    // Submit -> RT Review
    $service->approveRequest($request->id);
    expect($request->fresh()->workflow_status)->toBe('rt_review');

    // RT -> RW Review
    $service->approveRequest($request->id);
    expect($request->fresh()->workflow_status)->toBe('rw_review');

    // RW -> Admin Review
    $service->approveRequest($request->id);
    expect($request->fresh()->workflow_status)->toBe('admin_review');

    // Admin -> Approved
    $service->approveRequest($request->id);
    expect($request->fresh()->workflow_status)->toBe('approved');
    expect($request->fresh()->nomor_surat)->not->toBeNull();
});
