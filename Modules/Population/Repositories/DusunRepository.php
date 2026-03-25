<?php

declare(strict_types=1);

namespace Modules\Population\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Population\Contracts\Repositories\DusunRepositoryInterface;
use Modules\Population\Models\Dusun;

class DusunRepository extends BaseCrudRepository implements DusunRepositoryInterface
{
    public function __construct(Dusun $model)
    {
        parent::__construct($model);
    }
}
