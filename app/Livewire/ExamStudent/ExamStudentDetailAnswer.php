<?php

namespace App\Livewire\ExamStudent;

use App\Models\ExamStudent\ExamStudentDetail;
use App\Models\Question\QuestionItemAnswer;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ExamStudentDetailAnswer extends Component
{
    // use InteractsWithForms;

    public ?array $data = [];

    protected int | string | array $columnSpan = 'full';

    public $exam_student, $examStudentDetails = [];
    public $exam_student_detail;

    public function mount($examStudent) {
        $this->exam_student = $examStudent;

        $examStudentDetails = ExamStudentDetail::select('id')->where('exam_student_id', $this->exam_student)->orderBy('order','asc')->first();

        $this->exam_student_detail = $examStudentDetails->id;

        $this->details();

        $this->dispatch('examStudentDetailUpdated', $this->exam_student_detail);
    }

    protected $listeners = ['examStudentDetailLivewireUpdated' => 'getExamStudentDetailFromFilament'];

    public function getExamStudentDetailFromFilament($id)
    {
        $this->exam_student_detail = $id;
        $this->details();
    }

    public function choice($id) {
        $this->exam_student_detail = $id;

        $this->dispatch('examStudentDetailUpdated', $id);
    }

    public function details() {
        $examStudentDetails = ExamStudentDetail::select('id','status')->where('exam_student_id', $this->exam_student)->orderBy('order','asc')->get();

        $this->reset('examStudentDetails');

        foreach($examStudentDetails as $key => $examStudentDetail) {
            $this->examStudentDetails[] = [
                'id'=>$examStudentDetail->id,
                'label'=>$key + 1,
                'status'=>$examStudentDetail->status,
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.exam-student.exam-student-detail-answer');
    }
}
