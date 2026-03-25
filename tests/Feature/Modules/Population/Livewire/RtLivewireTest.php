<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Population\Livewire;

use Database\Seeders\RbacSeeder;
use Modules\Population\Models\Rw;
use Modules\Population\Models\Rt;
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

test('rt index page can be rendered', function () {
    $rw = Rw::factory()->create();
    Rt::factory()->count(3)->create(['rw_id' => $rw->id]);

    Livewire::test('pages::population.rt.index')
        ->assertOk()
        ->assertSee($rw->nomor);
});

test('rt can be created via livewire', function () {
    $rw = Rw::factory()->create();

    Livewire::test('pages::population.rt.form')
        ->set('rw_id', $rw->id)
        ->set('nomor', '002')
        ->set('ketua', 'Agus')
        ->call('save')
        ->assertRedirect(route('population.rt.index'));

    expect(Rt::where('nomor', '002')->exists())->toBeTrue();
});
