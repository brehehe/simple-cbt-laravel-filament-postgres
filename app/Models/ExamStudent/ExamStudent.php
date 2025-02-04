<?php

namespace App\Models\ExamStudent;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamStudent extends Model
{
    //
    use HasFactory, SoftDeletes, HasUuids;
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        // Tambahkan global scope untuk selalu mengurutkan berdasarkan created_at ASC
        static::addGlobalScope('orderByCreatedAt', function (Builder $query) {
            $query->orderBy('order', 'asc');
        });

        static::creating(function ($model) {
            // Jika kolom 'order' belum di-set, set dengan urutan terbesar + 1
            if (!$model->order) {
                $model->order = static::max('order') + 1;
            }
        });
    }

    public function details() {
        return $this->hasMany(ExamStudentDetail::class)->orderBy('order','asc');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
