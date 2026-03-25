<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Population\Livewire;

use Database\Seeders\RbacSeeder;
use Modules\Population\Models\KartuKeluarga;
use Modules\Population\Models\Penduduk;
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

test('penduduk index page can be rendered', function () {
    $kk = KartuKeluarga::factory()->create();
    Penduduk::factory()->count(3)->create(['kartu_keluarga_id' => $kk->id]);

    Livewire::test('pages::population.penduduk.index')
        ->assertOk()
        ->assertSee($kk->nomor_kk);
});

test('penduduk can be created via livewire', function () {
    $kk = KartuKeluarga::factory()->create();

    Livewire::test('pages::population.penduduk.form')
        ->set('kartu_keluarga_id', $kk->id)
        ->set('nik', '3201010101010001')
        ->set('nama', 'Warga Baru')
        ->set('tempat_lahir', 'Bandung')
        ->set('tanggal_lahir', '1990-01-01')
        ->set('jenis_kelamin', 'L')
        ->set('agama', 'Islam')
        ->set('status_perkawinan', 'Belum Kawin')
        ->set('pekerjaan', 'Wiraswasta')
        ->set('pendidikan_terakhir', 'SMA')
        ->set('golongan_darah', 'O')
        ->set('status_dalam_keluarga', 'Anak')
        ->set('kewarganegaraan', 'WNI')
        ->set('status', 'Aktif')
        ->call('save')
        ->assertRedirect(route('population.penduduk.index'));

    expect(Penduduk::where('nik', '3201010101010001')->exists())->toBeTrue();
});
