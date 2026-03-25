<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Volt\Volt;
use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->seed(\Database\Seeders\RbacSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('SuperAdmin');
});

it('can list users', function () {
    actingAs($this->admin);

    \Livewire\Livewire::test('pages::settings.users.index')
        ->assertStatus(200)
        ->assertSee($this->admin->name);
});

it('can create a new user', function () {
    actingAs($this->admin);

    \Livewire\Livewire::test('pages::settings.users.form')
        ->set('name', 'New User')
        ->set('email', 'new@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('selectedRoles', ['SuperAdmin'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    $user = User::where('email', 'new@example.com')->first();
    expect($user->hasRole('SuperAdmin'))->toBeTrue();
});
