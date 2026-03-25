<?php

declare(strict_types=1);

namespace Modules\Finance\Http\Requests;

use App\Core\Base\BaseRequest;

class StoreFinanceAccountRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'unique:finance_accounts,code'],
            'name' => $this->requiredString(),
            'type' => ['required', 'in:pemasukan,pengeluaran,pembiayaan'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
