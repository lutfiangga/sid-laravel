<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceAccount extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function searchable(): array
    {
        return ['code', 'name'];
    }

    public function filterable(): array
    {
        return ['type', 'is_active'];
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(FinanceBudget::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class);
    }
}
