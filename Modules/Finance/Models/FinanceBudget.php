<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceBudget extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'finance_period_id',
        'finance_account_id',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function searchable(): array
    {
        return ['notes'];
    }

    public function filterable(): array
    {
        return ['finance_period_id', 'finance_account_id'];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(FinancePeriod::class, 'finance_period_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinanceAccount::class, 'finance_account_id');
    }
}
