<?php

declare(strict_types=1);

namespace Modules\PublicService\Models;

use App\Core\Base\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Apparatus Model
 * 
 * Represents a village official/apparatus.
 */
class Apparatus extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'apparatus';

    protected $fillable = [
        'user_id',
        'nama',
        'jabatan',
        'nip',
        'foto',
        'status',
    ];

    /**
     * Searchable fields.
     */
    public function searchable(): array
    {
        return ['nama', 'jabatan', 'nip'];
    }

    /**
     * Filterable fields.
     */
    public function filterable(): array
    {
        return ['status'];
    }

    /**
     * Get the associated user account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
