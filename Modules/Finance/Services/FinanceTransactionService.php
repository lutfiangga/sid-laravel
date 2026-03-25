<?php

declare(strict_types=1);

namespace Modules\Finance\Services;

use App\Core\Base\BaseCrudService;
use Modules\Finance\Repositories\FinanceTransactionRepository;

class FinanceTransactionService extends BaseCrudService
{
    public function __construct(FinanceTransactionRepository $repository)
    {
        parent::__construct($repository);
    }
}
