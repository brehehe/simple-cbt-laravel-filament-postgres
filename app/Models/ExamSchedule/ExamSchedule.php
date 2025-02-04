<?php

namespace App\Models\ExamSchedule;

use App\Models\Question\Question;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ExamSchedule extends Model
{
    //
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    public function question() {
        return $this->belongsTo(Question::class);
    }

    protected static function booted()
    {
        Parent::boot();

        static::creating(function ($model) {
            // Pastikan pengguna sudah login
            if (Auth::check()) {
                $model->user_id = Auth::user()->id;
            }
            if (!$model->order) {
                $model->order = static::max('order') + 1;
            }
        });

        static::addGlobalScope('order', function ($query) {
            $query->orderBy('order', 'desc');
        });
    }
}
