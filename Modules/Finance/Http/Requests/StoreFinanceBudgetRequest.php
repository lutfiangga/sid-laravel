<?php

declare(strict_types=1);

namespace Modules\Finance\Http\Requests;

use App\Core\Base\BaseRequest;

class StoreFinanceBudgetRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'finance_period_id' => $this->requiredUuid()->exists('finance_periods', 'id'),
            'finance_account_id' => $this->requiredUuid()->exists('finance_accounts', 'id'),
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => $this->nullableString(),
        ];
    }
}
