<?php

namespace App\Traits;

use App\Models\ActivityLog\ActivityLog;

trait HasActivityLogs
{
    public static function bootHasActivityLogs()
    {
        static::created(function ($model) {
            self::logActivity('created', $model);
        });

        static::updated(function ($model) {
            self::logActivity('updated', $model);
        });

        static::deleted(function ($model) {
            self::logActivity('deleted', $model);
        });
    }

    protected static function logActivity($event_type, $model)
    {
        ActivityLog::create([
            'event_type'     => $event_type,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->id,
            'user_id'        => auth()?->id() ?? null,
            'old_values'     => $event_type === 'created' ? null : $model->getOriginal(),
            'new_values'     => $event_type === 'deleted' ? null : $model->getAttributes(),
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent()
        ]);
    }
}