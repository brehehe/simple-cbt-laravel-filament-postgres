<?php

namespace App\Models\Question;

use App\Models\Auth\User;
use App\Models\Subject\Subject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Question extends Model
{
    //
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    public function questionItems() {
        return $this->hasMany(QuestionItem::class);
    }

    public function questionItemAnswers() {
        return $this->hasMany(QuestionItemAnswer::class);
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        parent::boot();
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
