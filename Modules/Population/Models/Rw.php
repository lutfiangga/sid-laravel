<?php

declare(strict_types=1);

namespace Modules\Population\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rw extends BaseModel
{
    use HasFactory;
    protected $table = 'rws';

    protected $fillable = [
        'dusun_id',
        'nomor',
        'ketua',
    ];

    protected array $searchable = [
        'nomor',
        'ketua',
    ];

    protected array $filterable = [
        'dusun_id',
    ];

    public function dusun(): BelongsTo
    {
        return $this->belongsTo(Dusun::class, 'dusun_id', 'id');
    }

    public function rts(): HasMany
    {
        return $this->hasMany(Rt::class, 'rw_id', 'id');
    }
}
