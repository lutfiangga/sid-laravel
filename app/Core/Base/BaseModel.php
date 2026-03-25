<?php

declare(strict_types=1);

namespace App\Core\Base;

use App\Core\Traits\HasSearch;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

abstract class BaseModel extends Model
{
    use HasSearch, HasUuids, LogsActivity, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Columns available for search.
     *
     * @var array<int, string>
     */
    protected array $searchable = [];

    /**
     * Columns available for filtering.
     *
     * @var array<int, string>
     */
    protected array $filterable = [];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            $userId = Auth::id();
            if ($userId && $model->hasColumn('created_by')) {
                $model->created_by ??= $userId;
            }
        });

        static::updating(function (self $model): void {
            $userId = Auth::id();
            if ($userId && $model->hasColumn('updated_by')) {
                $model->updated_by = $userId;
            }
        });

        static::deleting(function (self $model): void {
            $userId = Auth::id();
            if ($userId && $model->hasColumn('deleted_by')) {
                $model->deleted_by = $userId;
                $model->saveQuietly();
            }
        });
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $eventName): string {
                return "{$this->getTable()} has been {$eventName}";
            });
    }

    /**
     * Get searchable columns.
     *
     * @return array<int, string>
     */
    public function getSearchable(): array
    {
        return $this->searchable;
    }

    /**
     * Get filterable columns.
     *
     * @return array<int, string>
     */
    public function getFilterable(): array
    {
        return $this->filterable;
    }

    /**
     * Check if the model's table has a given column.
     */
    protected function hasColumn(string $column): bool
    {
        return $this->getConnection()
            ->getSchemaBuilder()
            ->hasColumn($this->getTable(), $column);
    }
}
