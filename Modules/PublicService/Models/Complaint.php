<?php

declare(strict_types=1);

namespace Modules\PublicService\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Population\Models\Penduduk;

/**
 * Complaint Model
 * 
 * Represents a public complaint or aspiration submitted by a resident.
 */
class Complaint extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'penduduk_id',
        'title',
        'description',
        'status',
        'response',
    ];

    /**
     * Searchable fields.
     */
    public function searchable(): array
    {
        return ['title', 'description'];
    }

    /**
     * Filterable fields.
     */
    public function filterable(): array
    {
        return ['status'];
    }

    /**
     * Get the resident who submitted the complaint.
     */
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_id');
    }
}
