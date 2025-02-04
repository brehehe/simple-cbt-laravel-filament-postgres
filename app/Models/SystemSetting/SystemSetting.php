<?php

namespace App\Models\SystemSetting;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemSetting extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    protected static function booted()
    {
        parent::boot();

        static::addGlobalScope('order', function ($query) {
            $query->orderBy('order', 'desc');
        });

        static::creating(function ($model) {
            // Jika kolom 'order' belum di-set, set dengan urutan terbesar + 1
            if (!$model->order) {
                $model->order = static::max('order') + 1;
            }
        });
    }
}
