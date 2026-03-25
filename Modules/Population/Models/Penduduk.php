<?php

declare(strict_types=1);

namespace Modules\Population\Models;

use App\Core\Base\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penduduk extends BaseModel
{
    use HasFactory;

    protected $table = 'penduduks';

    protected $fillable = [
        'kartu_keluarga_id',
        'user_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'pendidikan_terakhir',
        'golongan_darah',
        'status_dalam_keluarga',
        'kewarganegaraan',
        'telepon',
        'email',
        'foto',
        'status',
    ];

    protected array $searchable = [
        'nik',
        'nama',
        'pekerjaan',
    ];

    protected array $filterable = [
        'kartu_keluarga_id',
        'jenis_kelamin',
        'status',
        'agama',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id', 'id');
    }

    /**
     * The user account linked to this resident.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
