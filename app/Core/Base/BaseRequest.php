<?php

declare(strict_types=1);

namespace App\Core\Base;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get a "required string" validation rule set.
     *
     * @return array<int, mixed>
     */
    protected function requiredString(int $max = 255): array
    {
        return ['required', 'string', "max:{$max}"];
    }

    /**
     * Get an "optional string" validation rule set.
     *
     * @return array<int, mixed>
     */
    protected function optionalString(int $max = 255): array
    {
        return ['nullable', 'string', "max:{$max}"];
    }

    /**
     * Get a validation rule that checks a UUID exists in a table.
     *
     * @return array<int, mixed>
     */
    protected function uuidExists(string $table, string $column = 'id'): array
    {
        return ['required', 'uuid', Rule::exists($table, $column)];
    }

    /**
     * Get a "required select/enum" validation rule set.
     *
     * @param  array<int, string>  $allowedValues
     * @return array<int, mixed>
     */
    protected function requiredEnum(array $allowedValues): array
    {
        return ['required', Rule::in($allowedValues)];
    }

    /**
     * Get a "required date" validation rule set.
     *
     * @return array<int, mixed>
     */
    protected function requiredDate(): array
    {
        return ['required', 'date'];
    }

    /**
     * Get an "optional date" validation rule set.
     *
     * @return array<int, mixed>
     */
    protected function optionalDate(): array
    {
        return ['nullable', 'date'];
    }
}
