<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\{actingAs, get};

beforeEach(function () {
    $this->seed(\Database\Seeders\RbacSeeder::class);
    
    $this->admin = User::factory()->create();
    $this->admin->assignRole('SuperAdmin');

    $this->limitedUser = User::factory()->create();
    $this->limitedUser->assignRole('RT');
});

it('shows sidebar menus based on permissions', function () {
    // Limited user should see Kependudukan but NOT Keuangan
    actingAs($this->limitedUser);
    
    get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee('Kependudukan')
        ->assertDontSee('Keuangan Desa (APBD)');

    // Admin should see both
    actingAs($this->admin);
    
    get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee('Kependudukan')
        ->assertSee('Keuangan Desa (APBD)');
});

it('denies access to settings if no permission', function () {
    actingAs($this->limitedUser);
    
    get(route('rbac.index'))->assertStatus(403);
    get(route('users.index'))->assertStatus(403);
});
