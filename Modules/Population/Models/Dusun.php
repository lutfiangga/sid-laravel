<?php

declare(strict_types=1);

namespace Modules\Population\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dusun extends BaseModel
{
    use HasFactory;
    protected $table = 'dusuns';

    protected $fillable = [
        'nama',
        'kode',
        'ketua',
    ];

    protected array $searchable = [
        'nama',
        'kode',
        'ketua',
    ];

    protected array $filterable = [
        'kode',
    ];

    public function rws(): HasMany
    {
        return $this->hasMany(Rw::class, 'dusun_id', 'id');
    }
}
