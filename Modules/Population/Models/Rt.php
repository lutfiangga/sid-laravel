<?php

declare(strict_types=1);

namespace Modules\Population\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rt extends BaseModel
{
    use HasFactory;
    protected $table = 'rts';

    protected $fillable = [
        'rw_id',
        'nomor',
        'ketua',
    ];

    protected array $searchable = [
        'nomor',
        'ketua',
    ];

    protected array $filterable = [
        'rw_id',
    ];

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class, 'rw_id', 'id');
    }

    public function kartuKeluargas(): HasMany
    {
        return $this->hasMany(KartuKeluarga::class, 'rt_id', 'id');
    }
}
