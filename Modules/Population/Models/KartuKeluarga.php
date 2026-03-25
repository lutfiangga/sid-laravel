<?php

declare(strict_types=1);

namespace Modules\Population\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KartuKeluarga extends BaseModel
{
    use HasFactory;
    protected $table = 'kartu_keluargas';

    protected $fillable = [
        'rt_id',
        'nomor_kk',
        'kepala_keluarga',
        'alamat',
    ];

    protected array $searchable = [
        'nomor_kk',
        'kepala_keluarga',
        'alamat',
    ];

    protected array $filterable = [
        'rt_id',
    ];

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id', 'id');
    }

    public function penduduks(): HasMany
    {
        return $this->hasMany(Penduduk::class, 'kartu_keluarga_id', 'id');
    }

    public function headOfFamily(): ?Penduduk
    {
        return $this->penduduks()->where('status_dalam_keluarga', 'Kepala Keluarga')->first();
    }
}
