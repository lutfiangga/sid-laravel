<?php

declare(strict_types=1);

namespace Modules\Population\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Population\Contracts\Repositories\RwRepositoryInterface;
use Modules\Population\Models\Rw;

class RwRepository extends BaseCrudRepository implements RwRepositoryInterface
{
    public function __construct(Rw $model)
    {
        parent::__construct($model);
    }
}
