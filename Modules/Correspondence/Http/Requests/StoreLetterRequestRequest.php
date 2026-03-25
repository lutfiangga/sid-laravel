<?php

declare(strict_types=1);

namespace Modules\Correspondence\Http\Requests;

use App\Core\Base\BaseRequest;

class StoreLetterRequestRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'letter_type_id' => ['required', 'uuid', 'exists:letter_types,id'],
            'data' => $this->requiredArray(),
        ];
    }
}
