<?php

declare(strict_types=1);

namespace Modules\PublicService\Http\Requests;

use App\Core\Base\BaseRequest;

class StoreComplaintRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'penduduk_id' => ['required', 'uuid', 'exists:penduduks,id'],
            'title' => $this->requiredString(),
            'description' => $this->requiredString(),
            'status' => ['required', 'in:pending,in_progress,resolved,rejected'],
            'response' => $this->nullableString(),
        ];
    }
}
