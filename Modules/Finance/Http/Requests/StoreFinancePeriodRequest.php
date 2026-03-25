<?php

declare(strict_types=1);

namespace Modules\Finance\Http\Requests;

use App\Core\Base\BaseRequest;

class StoreFinancePeriodRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:2000', 'max:2100', 'unique:finance_periods,year'],
            'description' => $this->nullableString(),
            'is_active' => ['required', 'boolean'],
        ];
    }
}
