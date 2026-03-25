<?php

declare(strict_types=1);

namespace Modules\Finance\Services;

use App\Core\Base\BaseCrudService;
use Modules\Finance\Repositories\FinancePeriodRepository;
use Illuminate\Support\Facades\DB;

class FinancePeriodService extends BaseCrudService
{
    public function __construct(FinancePeriodRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function beforeCreate(array $data): array
    {
        $this->ensureSingleActivePeriod($data);
        return $data;
    }

    protected function beforeUpdate(string $id, array $data): array
    {
        $this->ensureSingleActivePeriod($data);
        return $data;
    }

    /**
     * Ensure only one active period exists.
     */
    protected function ensureSingleActivePeriod(array $data): void
    {
        if (isset($data['is_active']) && $data['is_active']) {
            DB::table('finance_periods')->update(['is_active' => false]);
        }
    }
}
