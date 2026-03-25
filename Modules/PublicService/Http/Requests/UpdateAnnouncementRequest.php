<?php

declare(strict_types=1);

namespace Modules\PublicService\Http\Requests;

use App\Core\Base\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateAnnouncementRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => $this->requiredString(),
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('announcements', 'slug')->ignore($this->route('announcement')),
            ],
            'content' => $this->requiredString(),
            'is_published' => ['required', 'boolean'],
            'author_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
