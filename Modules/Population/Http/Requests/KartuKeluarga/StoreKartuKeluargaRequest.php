<?php

declare(strict_types=1);

namespace Modules\Population\Http\Requests\KartuKeluarga;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class StoreKartuKeluargaRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'rt_id' => ['required', 'uuid', Rule::exists('rts', 'id')],
            'nomor_kk' => ['required', 'string', 'size:16', 'unique:kartu_keluargas,nomor_kk'],
            'kepala_keluarga' => $this->requiredString(),
            'alamat' => $this->requiredString(500),
        ];
    }
}
