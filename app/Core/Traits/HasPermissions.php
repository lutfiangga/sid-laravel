<?php

declare(strict_types=1);

namespace App\Core\Traits;

/**
 * HasPermissions trait — convenience wrapper around spatie HasRoles.
 *
 * Use this on models that need module-level permission checks
 * using the "module.action" format (e.g. "penduduk.view").
 */
trait HasPermissions
{
    /**
     * Check if the user has a specific module permission.
     */
    public function hasModulePermission(string $module, string $action): bool
    {
        return $this->hasPermissionTo("{$module}.{$action}");
    }

    /**
     * Check if the user has any permission for a module.
     *
     * @param  array<int, string>  $actions
     */
    public function hasAnyModulePermission(string $module, array $actions): bool
    {
        $permissions = array_map(
            fn (string $action): string => "{$module}.{$action}",
            $actions,
        );

        return $this->hasAnyPermission($permissions);
    }

    /**
     * Check if the user has all permissions for a module.
     *
     * @param  array<int, string>  $actions
     */
    public function hasAllModulePermissions(string $module, array $actions): bool
    {
        $permissions = array_map(
            fn (string $action): string => "{$module}.{$action}",
            $actions,
        );

        return $this->hasAllPermissions($permissions);
    }
}
