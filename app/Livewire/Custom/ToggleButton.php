<?php

namespace App\Livewire\Custom;

use App\Models\ExamStudent\ExamStudent;
use App\Models\ExamStudent\ExamStudentDetail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ToggleButton extends Component
{
    public $exam_student;

    public $examStudentDetails = [];

    // Status yang tersedia
    protected $statuses = ['belum', 'ragu-ragu', 'terpilih'];

    public function mount()
    {
        if (env('APP_ENV') != 'local') {
            $check_exam = json_decode(Redis::get('exam_student_id'), true)['id']; // Decode as an array
        } else {
            $check_exam = json_decode(Session::get('exam_student_id'), true)['id']; // Decode as an array
        }
        $this->exam_student = ExamStudent::find($check_exam);

        // Ambil data dari database dan hanya gunakan angka untuk tombol
        $this->examStudentDetails = ExamStudentDetail::where('exam_student_id', $this->exam_student->id)
            ->get()
            ->mapWithKeys(function ($detail, $index) {
                return [
                    $detail->id => [
                        'number' => $index + 1, // Menampilkan angka sebagai label
                        'status' => $detail->status, // Status default
                    ],
                ];
            })
            ->toArray();
    }

    // Metode untuk memperbarui status toggle
    public function toggleStatus($id)
    {
        // Menentukan status berikutnya berdasarkan status saat ini
        $currentStatus = $this->examStudentDetails[$id]['status'];
        $currentIndex = array_search($currentStatus, $this->statuses);
        // $nextIndex = ($currentIndex + 1) % count($this->statuses);

        // Memperbarui status ke status berikutnya
        // $this->examStudentDetails[$id]['status'] = $this->statuses[$nextIndex];
    }

    public function render()
    {
        return view('livewire.custom.toggle-button');
    }
}
