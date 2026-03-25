<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Population\Livewire;

use Database\Seeders\RbacSeeder;
use Modules\Population\Models\Dusun;
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

test('dusun index page can be rendered', function () {
    Livewire::test('pages::population.dusun.index')
        ->assertStatus(200);
});

test('dusun can be created via livewire', function () {
    Livewire::test('pages::population.dusun.form')
        ->set('kode', 'DSNTEST')
        ->set('nama', 'Test Dusun')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('population.dusun.index'));

    expect(Dusun::where('kode', 'DSNTEST')->exists())->toBeTrue();
});
