<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->seed(\Database\Seeders\RbacSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('SuperAdmin');
});

it('can list roles', function () {
    actingAs($this->admin);

    \Livewire\Livewire::test('pages::settings.rbac.index')
        ->assertStatus(200)
        ->assertSee('SuperAdmin');
});

it('can create a new role with permissions', function () {
    actingAs($this->admin);

    Permission::create(['name' => 'test.permission']);

    \Livewire\Livewire::test('pages::settings.rbac.form')
        ->set('name', 'New Role')
        ->set('selectedPermissions', ['test.permission'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('rbac.index'));

    $this->assertDatabaseHas('roles', ['name' => 'New Role']);
    $role = Role::where('name', 'New Role')->first();
    expect($role->hasPermissionTo('test.permission'))->toBeTrue();
});
