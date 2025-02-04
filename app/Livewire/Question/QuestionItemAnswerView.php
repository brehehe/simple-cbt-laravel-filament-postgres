<?php

namespace App\Livewire\Question;

use App\Models\Question\QuestionItemAnswer;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class QuestionItemAnswerView extends Component {

    public function render(): View
    {
        return view('livewire.question.question-item-answer-view');
    }
}
