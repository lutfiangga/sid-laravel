<?php

declare(strict_types=1);

namespace Modules\System\Repositories;

use App\Core\Base\BaseCrudRepository;
use Spatie\Permission\Models\Role;
use Modules\System\Contracts\Repositories\RoleRepositoryInterface;

class RoleRepository extends BaseCrudRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
