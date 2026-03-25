<?php

declare(strict_types=1);

namespace Modules\Correspondence\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * WorkflowLog Model
 * 
 * Tracks the history of workflow transitions for a letter request.
 */
class WorkflowLog extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'request_id',
        'action',
        'actor_id',
        'note',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the letter request this log belongs to.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(LetterRequest::class, 'request_id');
    }

    /**
     * Get the user who performed the action.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
