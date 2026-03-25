<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancePeriod extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'year',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function searchable(): array
    {
        return ['year', 'description'];
    }

    public function filterable(): array
    {
        return ['is_active'];
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
