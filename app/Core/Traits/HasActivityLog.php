<?php

declare(strict_types=1);

namespace App\Core\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * HasActivityLog trait — configures spatie/laravel-activitylog
 * with sensible defaults for SID modules.
 */
trait HasActivityLog
{
    use LogsActivity;

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
}
