<?php

namespace App\Models\ExamStudent;

use App\Models\Question\QuestionItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamStudentDetail extends Model
{
    //
    use HasFactory, SoftDeletes, HasUuids;
    protected $guarded = ['id'];

    public function questionItem() {
        return $this->belongsTo(QuestionItem::class,'question_item_id','id');
    }

    protected static function booted()
    {
        parent::boot();

        static::addGlobalScope('order', function ($query) {
            $query->orderBy('order', 'asc');
        });

        static::creating(function ($model) {
            // Jika kolom 'order' belum di-set, set dengan urutan terbesar + 1
            if (!$model->order) {
                $model->order = static::max('order') + 1;
            }
        });
    }
}
