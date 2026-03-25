<?php

declare(strict_types=1);

namespace Modules\Correspondence\Models;

use App\Core\Base\BaseModel;
use App\Core\Traits\HasApproval;
use App\Core\Traits\HasWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Population\Models\Penduduk;

/**
 * LetterRequest Model
 *
 * Represents a specific instance of a letter requested by a resident.
 */
class LetterRequest extends BaseModel
{
    use HasApproval, HasFactory, HasWorkflow, SoftDeletes;

    protected $fillable = [
        'penduduk_id',
        'letter_type_id',
        'nomor_surat',
        'data',
        'attachments',
        'workflow_status',
        'rejection_reason',
        'current_official_id',
    ];

    protected $casts = [
        'data' => 'array',
        'attachments' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (! $model->workflow_status) {
                $model->workflow_status = 'draft';
            }
        });
    }

    /**
     * Searchable fields.
     */
    public function searchable(): array
    {
        return ['nomor_surat', 'workflow_status'];
    }

    /**
     * Get the resident who requested the letter.
     */
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_id');
    }

    /**
     * Get the letter type template.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    /**
     * Get the workflow logs for this request.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class, 'request_id');
    }
}
