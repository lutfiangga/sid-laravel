<?php

declare(strict_types=1);

namespace Modules\Correspondence\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * LetterType Model
 * 
 * Represents a document template (e.g. SKTM, SKU).
 */
class LetterType extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'kode',
        'template',
        'requirement_list',
    ];

    protected $casts = [
        'requirement_list' => 'array',
    ];

    /**
     * Searchable fields.
     */
    public function searchable(): array
    {
        return ['nama', 'kode'];
    }

    /**
     * Get all requests for this letter type.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(LetterRequest::class, 'letter_type_id');
    }
}
