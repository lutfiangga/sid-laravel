<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    /**
     * Standard actions available for each module.
     *
     * @var array<int, string>
     */
    private const STANDARD_ACTIONS = [
        'view',
        'create',
        'update',
        'delete',
        'approve',
        'reject',
        'print',
        'export',
        'audit',
    ];

    /**
     * Modules that should have permissions created.
     *
     * @var array<int, string>
     */
    private const MODULES = [
        'system',
        'penduduk',
        'kartu-keluarga',
        'rt',
        'rw',
        'dusun',
        'mutasi',
        'surat',
        'health',
        'inventory',
        'finance',
        'complaint',
        'events',
        'security',
        'development',
        'rbac',
        'workflow',
        'generator',
        'announcement',
        'apparatus',
        'letter-type',
        'letter-request',
        'finance-period',
        'finance-account',
        'finance-budget',
        'finance-transaction',
    ];

    /**
     * Role definitions.
     *
     * @var array<int, string>
     */
    private const ROLES = [
        'SuperAdmin',
        'VillageHead',
        'Secretary',
        'Admin',
        'Operator',
        'RT',
        'RW',
        'HealthOfficer',
        'FinanceOfficer',
        'Warga',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for each module
        $allPermissions = [];
        foreach (self::MODULES as $module) {
            foreach (self::STANDARD_ACTIONS as $action) {
                $permissionName = "{$module}.{$action}";
                Permission::firstOrCreate(['name' => $permissionName]);
                $allPermissions[] = $permissionName;
            }
        }

        // Create roles
        foreach (self::ROLES as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // SuperAdmin gets all permissions
        $superAdmin = Role::findByName('SuperAdmin');
        $superAdmin->syncPermissions($allPermissions);

        // VillageHead gets view, approve, reject, print, export for all
        $villageHead = Role::findByName('VillageHead');
        $villageHead->syncPermissions(
            $this->getPermissionsForActions(self::MODULES, ['view', 'approve', 'reject', 'print', 'export'])
        );

        // Secretary gets full CRUD + print/export for core modules
        $secretary = Role::findByName('Secretary');
        $secretary->syncPermissions(
            $this->getPermissionsForActions(
                ['penduduk', 'kartu-keluarga', 'surat', 'events', 'complaint', 'announcement', 'letter-type', 'letter-request'],
                ['view', 'create', 'update', 'delete', 'print', 'export']
            )
        );

        // Admin gets full CRUD for admin-level modules
        $admin = Role::findByName('Admin');
        $admin->syncPermissions(
            $this->getPermissionsForActions(
                ['penduduk', 'kartu-keluarga', 'rt', 'rw', 'dusun', 'mutasi', 'surat', 'system', 'announcement', 'apparatus', 'letter-type', 'letter-request'],
                ['view', 'create', 'update', 'delete', 'approve', 'print', 'export']
            )
        );

        // Operator gets view + create + update
        $operator = Role::findByName('Operator');
        $operator->syncPermissions(
            $this->getPermissionsForActions(
                ['penduduk', 'kartu-keluarga', 'surat', 'complaint', 'announcement', 'letter-request'],
                ['view', 'create', 'update']
            )
        );

        // RT gets limited view + create
        $rt = Role::findByName('RT');
        $rt->syncPermissions(
            $this->getPermissionsForActions(
                ['penduduk', 'kartu-keluarga', 'surat', 'complaint', 'events', 'announcement', 'letter-request'],
                ['view', 'create']
            )
        );

        // RW gets RT permissions + approve
        $rw = Role::findByName('RW');
        $rw->syncPermissions(
            $this->getPermissionsForActions(
                ['penduduk', 'kartu-keluarga', 'surat', 'complaint', 'events', 'announcement', 'letter-request'],
                ['view', 'create', 'approve', 'reject']
            )
        );

        // HealthOfficer gets full access to health module
        $healthOfficer = Role::findByName('HealthOfficer');
        $healthOfficer->syncPermissions(
            array_merge(
                $this->getPermissionsForActions(['health'], self::STANDARD_ACTIONS),
                $this->getPermissionsForActions(['penduduk', 'kartu-keluarga'], ['view']),
            )
        );

        // FinanceOfficer gets full access to finance module
        $financeOfficer = Role::findByName('FinanceOfficer');
        $financeOfficer->syncPermissions(
            array_merge(
                $this->getPermissionsForActions(['finance', 'finance-period', 'finance-account', 'finance-budget', 'finance-transaction'], self::STANDARD_ACTIONS),
                $this->getPermissionsForActions(['penduduk'], ['view']),
            )
        );

        // Warga gets scoped access: only their own letter requests & complaints, read-only announcements
        $warga = Role::findByName('Warga');
        $warga->syncPermissions(
            $this->getPermissionsForActions(
                ['letter-request', 'complaint'],
                ['view', 'create', 'delete']
            ) +
            $this->getPermissionsForActions(['announcement'], ['view'])
        );
    }

    /**
     * Get permission names for given modules and actions.
     *
     * @param  array<int, string>  $modules
     * @param  array<int, string>  $actions
     * @return array<int, string>
     */
    private function getPermissionsForActions(array $modules, array $actions): array
    {
        $permissions = [];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[] = "{$module}.{$action}";
            }
        }

        return $permissions;
    }
}
