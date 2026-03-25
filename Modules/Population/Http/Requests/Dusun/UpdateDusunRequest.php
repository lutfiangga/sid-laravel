<?php

declare(strict_types=1);

namespace Modules\Population\Http\Requests\Dusun;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateDusunRequest extends BaseRequest
{
    public function rules(): array
    {
        $dusunId = $this->route('dusun');

        return [
            'nama' => $this->requiredString(),
            'kode' => [
                ...$this->requiredString(10),
                Rule::unique('dusuns', 'kode')->ignore($dusunId),
            ],
            'ketua' => $this->optionalString(),
        ];
    }
}
