<?php

namespace App\Livewire;

use App\Filament\Pages\ExamStudent\ExamStudent;
use App\Models\ExamStudent\ExamStudent as ExamStudentExamStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CountdownTimer extends Component
{
    public $data, $remainingTime, $checkWaktu;

    public function mount($examStudent)
    {
        $this->data = $examStudent;

        $this->cekDate();
    }

    public function cekDate() {
        $event = ExamStudentExamStudent::select('created_at','minutes')->find($this->data);

        $createdAt = Carbon::parse($event->created_at);
        $now = Carbon::now();
        $endTime = $createdAt->addMinutes((int) $event->minutes);

        $remainingTime = $endTime->timestamp - $now->timestamp;

        $this->remainingTime = $remainingTime;
    }

    public function processExam() {
        $this->dispatch('processExam');
    }

    public function render()
    {
        return view('livewire.countdown-timer');
    }
}
