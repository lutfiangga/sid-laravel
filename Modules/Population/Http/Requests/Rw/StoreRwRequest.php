<?php

declare(strict_types=1);

namespace Modules\Population\Http\Requests\Rw;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRwRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'dusun_id' => ['required', 'uuid', Rule::exists('dusuns', 'id')],
            'nomor' => [
                'required',
                'string',
                'max:3',
                Rule::unique('rws', 'nomor')->where('dusun_id', $this->dusun_id),
            ],
            'ketua' => $this->optionalString(),
        ];
    }
}
