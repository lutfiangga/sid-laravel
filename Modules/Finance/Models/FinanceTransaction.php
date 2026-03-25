<?php

declare(strict_types=1);

namespace Modules\Finance\Models;

use App\Core\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceTransaction extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'finance_period_id',
        'finance_account_id',
        'type',
        'transaction_date',
        'amount',
        'description',
        'evidence_file',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function searchable(): array
    {
        return ['description'];
    }

    public function filterable(): array
    {
        return ['finance_period_id', 'finance_account_id', 'type', 'transaction_date'];
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
