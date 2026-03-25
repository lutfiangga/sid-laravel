<?php

declare(strict_types=1);

namespace Modules\Population\Http\Requests\Rt;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRtRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'rw_id' => ['required', 'uuid', Rule::exists('rws', 'id')],
            'nomor' => [
                'required',
                'string',
                'max:3',
                Rule::unique('rts', 'nomor')->where('rw_id', $this->rw_id),
            ],
            'ketua' => $this->optionalString(),
        ];
    }
}
