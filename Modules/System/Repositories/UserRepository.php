<?php

declare(strict_types=1);

namespace Modules\System\Repositories;

use App\Core\Base\BaseCrudRepository;
use App\Models\User;
use Modules\System\Contracts\Repositories\UserRepositoryInterface;

class UserRepository extends BaseCrudRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
