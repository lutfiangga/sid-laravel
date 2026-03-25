<?php

declare(strict_types=1);

namespace Modules\Population\Http\Requests\Rw;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateRwRequest extends BaseRequest
{
    public function rules(): array
    {
        $rwId = $this->route('rw');

        return [
            'dusun_id' => ['required', 'uuid', Rule::exists('dusuns', 'id')],
            'nomor' => [
                'required',
                'string',
                'max:3',
                Rule::unique('rws', 'nomor')
                    ->where('dusun_id', $this->dusun_id)
                    ->ignore($rwId),
            ],
            'ketua' => $this->optionalString(),
        ];
    }
}
