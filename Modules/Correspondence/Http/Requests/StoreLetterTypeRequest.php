<?php

declare(strict_types=1);

namespace Modules\Correspondence\Http\Requests;

use App\Core\Base\BaseRequest;

class StoreLetterTypeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama' => $this->requiredString(),
            'kode' => ['required', 'string', 'unique:letter_types,kode'],
            'template' => $this->requiredString(),
            'requirement_list' => $this->nullableArray(),
        ];
    }
}
