<?php

declare(strict_types=1);

namespace Modules\Finance\Repositories;

use App\Core\Base\BaseCrudRepository;
use Modules\Finance\Models\FinanceBudget;

class FinanceBudgetRepository extends BaseCrudRepository
{
    public function __construct(FinanceBudget $model)
    {
        parent::__construct($model);
    }
}
