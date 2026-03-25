<?php

declare(strict_types=1);

namespace Modules\Finance\Http\Requests;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateFinancePeriodRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'year' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
                Rule::unique('finance_periods', 'year')->ignore($this->route('finance_period') ?? $this->route('period')),
            ],
            'description' => $this->nullableString(),
            'is_active' => ['required', 'boolean'],
        ];
    }
}
