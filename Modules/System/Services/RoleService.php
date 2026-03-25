<?php

declare(strict_types=1);

namespace Modules\System\Services;

use App\Core\Base\BaseCrudService;
use Spatie\Permission\Models\Role;
use Modules\System\Contracts\Repositories\RoleRepositoryInterface;
use Modules\System\Contracts\Services\RoleServiceInterface;

class RoleService extends BaseCrudService implements RoleServiceInterface
{
    public function __construct(RoleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Synchronize permissions for a specific role.
     *
     * @param Role $role
     * @param array<int, string> $permissions List of permission names
     * @return Role
     */
    public function syncPermissions(Role $role, array $permissions): Role
    {
        $role->syncPermissions($permissions);

        return $role;
    }
}
