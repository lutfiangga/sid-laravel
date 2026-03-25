<?php

declare(strict_types=1);

namespace Modules\Population\Http\Requests\KartuKeluarga;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateKartuKeluargaRequest extends BaseRequest
{
    public function rules(): array
    {
        $kkId = $this->route('kartu_keluarga');

        return [
            'rt_id' => ['required', 'uuid', Rule::exists('rts', 'id')],
            'nomor_kk' => [
                'required',
                'string',
                'size:16',
                Rule::unique('kartu_keluargas', 'nomor_kk')->ignore($kkId),
            ],
            'kepala_keluarga' => $this->requiredString(),
            'alamat' => $this->requiredString(500),
        ];
    }
}
