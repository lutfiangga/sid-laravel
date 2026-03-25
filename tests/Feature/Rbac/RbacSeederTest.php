<?php

declare(strict_types=1);

use Database\Seeders\RbacSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RbacSeeder::class);
});

it('creates all nine roles', function () {
    $roles = Role::pluck('name')->toArray();

    expect($roles)->toContain('SuperAdmin')
        ->toContain('VillageHead')
        ->toContain('Secretary')
        ->toContain('Admin')
        ->toContain('Operator')
        ->toContain('RT')
        ->toContain('RW')
        ->toContain('HealthOfficer')
        ->toContain('FinanceOfficer');
});

it('creates permissions for all modules', function () {
    $modules = [
        'system', 'penduduk', 'kartu-keluarga', 'rt', 'rw', 'dusun',
        'mutasi', 'surat', 'health', 'inventory', 'finance', 'complaint',
        'events', 'security', 'development', 'rbac', 'workflow', 'generator',
    ];

    foreach ($modules as $module) {
        expect(Permission::where('name', "{$module}.view")->exists())->toBeTrue(
            "Permission {$module}.view should exist"
        );
    }
});

it('assigns all permissions to SuperAdmin', function () {
    $superAdmin = Role::findByName('SuperAdmin');
    $totalPermissions = Permission::count();

    expect($superAdmin->permissions->count())->toBe($totalPermissions);
});

it('assigns view and approve permissions to VillageHead', function () {
    $villageHead = Role::findByName('VillageHead');

    expect($villageHead->hasPermissionTo('penduduk.view'))->toBeTrue()
        ->and($villageHead->hasPermissionTo('surat.approve'))->toBeTrue()
        ->and($villageHead->hasPermissionTo('penduduk.create'))->toBeFalse();
});

it('assigns limited permissions to RT role', function () {
    $rt = Role::findByName('RT');

    expect($rt->hasPermissionTo('penduduk.view'))->toBeTrue()
        ->and($rt->hasPermissionTo('surat.create'))->toBeTrue()
        ->and($rt->hasPermissionTo('surat.delete'))->toBeFalse()
        ->and($rt->hasPermissionTo('rbac.view'))->toBeFalse();
});

it('assigns health module access to HealthOfficer', function () {
    $healthOfficer = Role::findByName('HealthOfficer');

    expect($healthOfficer->hasPermissionTo('health.view'))->toBeTrue()
        ->and($healthOfficer->hasPermissionTo('health.create'))->toBeTrue()
        ->and($healthOfficer->hasPermissionTo('penduduk.view'))->toBeTrue()
        ->and($healthOfficer->hasPermissionTo('finance.view'))->toBeFalse();
});

it('is idempotent and can be run multiple times', function () {
    // Seed again
    $this->seed(RbacSeeder::class);

    $roleCount = Role::count();
    expect($roleCount)->toBe(9);
});
