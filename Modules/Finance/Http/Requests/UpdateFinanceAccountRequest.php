<?php

declare(strict_types=1);

namespace Modules\Finance\Http\Requests;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateFinanceAccountRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('finance_accounts', 'code')->ignore($this->route('finance_account') ?? $this->route('account')),
            ],
            'name' => $this->requiredString(),
            'type' => ['required', 'in:pemasukan,pengeluaran,pembiayaan'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
