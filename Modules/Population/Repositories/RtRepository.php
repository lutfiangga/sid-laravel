<?php

declare(strict_types=1);

namespace Modules\Population\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Population\Contracts\Repositories\RtRepositoryInterface;
use Modules\Population\Models\Rt;

class RtRepository extends BaseCrudRepository implements RtRepositoryInterface
{
    public function __construct(Rt $model)
    {
        parent::__construct($model);
    }
}
