<?php

declare(strict_types=1);

namespace Modules\Population\Http\Requests\Penduduk;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UpdatePendudukRequest extends BaseRequest
{
    public function rules(): array
    {
        $pendudukId = $this->route('penduduk');

        return [
            'kartu_keluarga_id' => ['required', 'uuid', Rule::exists('kartu_keluargas', 'id')],
            'nik' => [
                'required',
                'string',
                'size:16',
                Rule::unique('penduduks', 'nik')->ignore($pendudukId),
            ],
            'nama' => $this->requiredString(),
            'tempat_lahir' => $this->requiredString(),
            'tanggal_lahir' => $this->requiredDate(),
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'agama' => $this->requiredString(),
            'status_perkawinan' => $this->requiredString(),
            'pekerjaan' => $this->requiredString(),
            'pendidikan_terakhir' => $this->requiredString(),
            'golongan_darah' => $this->requiredString(3),
            'status_dalam_keluarga' => $this->requiredString(),
            'kewarganegaraan' => ['required', 'string', 'max:50'],
            'telepon' => $this->optionalString(20),
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'string', 'max:20'],
        ];
    }
}
