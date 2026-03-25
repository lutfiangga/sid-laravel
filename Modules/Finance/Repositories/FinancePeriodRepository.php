<?php

declare(strict_types=1);

namespace Modules\Finance\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Finance\Models\FinancePeriod;

class FinancePeriodRepository extends BaseCrudRepository
{
    public function __construct(FinancePeriod $model)
    {
        parent::__construct($model);
    }
}
