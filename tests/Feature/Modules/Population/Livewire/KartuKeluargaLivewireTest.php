<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Population\Livewire;

use Database\Seeders\RbacSeeder;
use Modules\Population\Models\Rt;
use Modules\Population\Models\KartuKeluarga;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RbacSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('SuperAdmin');
    $this->actingAs($this->user);
});

test('kk index page can be rendered', function () {
    $rt = Rt::factory()->create();
    KartuKeluarga::factory()->count(3)->create(['rt_id' => $rt->id]);

    Livewire::test('pages::population.kartu-keluarga.index')
        ->assertOk()
        ->assertSee('RT ' . $rt->nomor);
});

test('kk can be created via livewire', function () {
    $rt = Rt::factory()->create();

    Livewire::test('pages::population.kartu-keluarga.form')
        ->set('rt_id', $rt->id)
        ->set('nomor_kk', '1234567890123456')
        ->set('kepala_keluarga', 'Suwardi')
        ->set('alamat', 'Jl. Merdeka No. 1')
        ->call('save')
        ->assertRedirect(route('population.kartu-keluarga.index'));

    expect(KartuKeluarga::where('nomor_kk', '1234567890123456')->exists())->toBeTrue();
});
