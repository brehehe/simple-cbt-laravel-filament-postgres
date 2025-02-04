<?php

namespace App\Models\Subject;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    //
    use HasFactory, HasUuids, SoftDeletes;
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
