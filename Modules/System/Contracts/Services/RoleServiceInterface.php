<?php

declare(strict_types=1);

namespace Modules\System\Contracts\Services;

use App\Core\Contracts\BaseCrudServiceInterface;
use Spatie\Permission\Models\Role;

interface RoleServiceInterface extends BaseCrudServiceInterface
{
    /**
     * Synchronize permissions for a specific role.
     *
     * @param Role $role
     * @param array<int, string> $permissions List of permission names
     * @return Role
     */
    public function syncPermissions(Role $role, array $permissions): Role;
}
