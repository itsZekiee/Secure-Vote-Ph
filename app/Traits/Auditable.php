<?php

namespace App\Traits;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            AuditLogger::log(
                'CREATE',
                static::getModuleForAudit(),
                "Created new " . static::getModuleForAudit() . ": " . ($model->name ?? $model->title ?? $model->id),
                null,
                $model->getAttributes()
            );
        });

        static::updated(function (Model $model) {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getDirty());
            $newValues = $model->getDirty();

            AuditLogger::log(
                'UPDATE',
                static::getModuleForAudit(),
                "Updated " . static::getModuleForAudit() . ": " . ($model->name ?? $model->title ?? $model->id),
                $oldValues,
                $newValues
            );
        });

        static::deleted(function (Model $model) {
            AuditLogger::log(
                'DELETE',
                static::getModuleForAudit(),
                "Deleted " . static::getModuleForAudit() . ": " . ($model->name ?? $model->title ?? $model->id),
                $model->getAttributes(),
                null
            );
        });
    }

    protected static function getModuleForAudit(): string
    {
        return class_basename(static::class);
    }
}
