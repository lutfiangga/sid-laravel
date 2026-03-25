<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Population\Livewire;

use Database\Seeders\RbacSeeder;
use Modules\Population\Models\Dusun;
use Modules\Population\Models\Rw;
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

test('rw index page can be rendered', function () {
    $dusun = Dusun::factory()->create();
    Rw::factory()->count(3)->create(['dusun_id' => $dusun->id]);

    Livewire::test('pages::population.rw.index')
        ->assertOk()
        ->assertSee($dusun->nama);
});

test('rw can be created via livewire', function () {
    $dusun = Dusun::factory()->create();

    Livewire::test('pages::population.rw.form')
        ->set('dusun_id', $dusun->id)
        ->set('nomor', '001')
        ->set('ketua', 'Budi')
        ->call('save')
        ->assertRedirect(route('population.rw.index'));

    expect(Rw::where('nomor', '001')->exists())->toBeTrue();
});
