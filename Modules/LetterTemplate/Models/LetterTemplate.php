<?php

declare(strict_types=1);

namespace Modules\LetterTemplate\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * LetterTemplate Model
 *
 * Represents a document template for generating letters.
 */
class LetterTemplate extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'kode',
        'content',
        'placeholders',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'orientation',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'margin_top' => 'integer',
        'margin_bottom' => 'integer',
        'margin_left' => 'integer',
        'margin_right' => 'integer',
    ];

    /**
     * Searchable fields.
     */
    public function searchable(): array
    {
        return ['nama', 'kode', 'content'];
    }

    /**
     * Filterable fields.
     */
    public function filterable(): array
    {
        return ['orientation'];
    }
}
