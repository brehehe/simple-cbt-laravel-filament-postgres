<?php

namespace App\Models\Question;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionItem extends Model
{
    //
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function questionItemAnswers() {
        return $this->hasMany(QuestionItemAnswer::class)->orderBy('alphabet','asc');
    }

    public function questionItemAnswerCorrect() {
        return $this->belongsTo(QuestionItemAnswer::class,'question_item_answer_id','id');
    }

    protected static function booted()
    {
        parent::boot();
        static::saved(function ($model) {
            // Mengambil semua jawaban terkait dan mengurutkannya berdasarkan waktu pembuatan (created_at)
            $answers = $model->questionItemAnswers()->orderBy('created_at')->get();

            // Mengupdate setiap jawaban dengan urutan alphabet yang baru
            $answers->each(function ($answer, $index) {
                $alphabet = chr(65 + $index); // Menghasilkan alphabet berdasarkan urutan (A, B, C, ...)
                $answer->update(['alphabet' => $alphabet]); // Memperbarui kolom 'alphabet'
            });
        });

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
