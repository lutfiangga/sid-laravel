<?php

declare(strict_types=1);

namespace Modules\Finance\Services;

use App\Core\Base\BaseCrudService;
use Modules\Finance\Repositories\FinanceBudgetRepository;

class FinanceBudgetService extends BaseCrudService
{
    public function __construct(FinanceBudgetRepository $repository)
    {
        parent::__construct($repository);
    }
}
