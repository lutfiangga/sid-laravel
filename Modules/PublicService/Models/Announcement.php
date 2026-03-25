<?php

declare(strict_types=1);

namespace Modules\PublicService\Models;

use App\Core\Base\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Announcement Model
 *
 * Represents a news article or announcement for the village.
 */
class Announcement extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'cover_image',
        'is_published',
        'author_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Searchable fields.
     */
    public function searchable(): array
    {
        return ['title', 'content'];
    }

    /**
     * Filterable fields.
     */
    public function filterable(): array
    {
        return ['is_published'];
    }

    /**
     * Get the author of the announcement.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
