<?php

declare(strict_types=1);

namespace Modules\Population\Http\Requests\Dusun;

use App\Core\Base\BaseRequest;

class StoreDusunRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'nama' => $this->requiredString(),
            'kode' => [...$this->requiredString(10), 'unique:dusuns,kode'],
            'ketua' => $this->optionalString(),
        ];
    }
}
