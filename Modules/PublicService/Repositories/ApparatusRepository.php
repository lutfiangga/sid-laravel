<?php

declare(strict_types=1);

namespace Modules\PublicService\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\PublicService\Models\Apparatus;

class ApparatusRepository extends BaseCrudRepository
{
    public function __construct(Apparatus $model)
    {
        parent::__construct($model);
    }
}
