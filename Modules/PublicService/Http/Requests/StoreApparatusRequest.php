<?php

declare(strict_types=1);

namespace Modules\PublicService\Http\Requests;

use App\Core\Base\BaseRequest;

class StoreApparatusRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'nama' => $this->requiredString(),
            'jabatan' => $this->requiredString(),
            'nip' => $this->nullableString(),
            'foto' => $this->nullableString(),
            'status' => ['required', 'in:aktif,tidak_aktif'],
        ];
    }
}
