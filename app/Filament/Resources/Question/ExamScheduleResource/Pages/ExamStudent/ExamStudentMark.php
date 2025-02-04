<?php

namespace App\Filament\Resources\Question\ExamScheduleResource\Pages\ExamStudent;

use App\Filament\Pages\ExamStudent\ExamStudent;
use App\Filament\Resources\Question\ExamScheduleResource;
use App\Models\ExamStudent\ExamStudent as ExamStudentExamStudent;
use Egulias\EmailValidator\Warning\TLD;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ExamStudentMark extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ExamScheduleResource::class;

    protected static string $view = 'filament.resources.question.exam-schedule-resource.pages.exam-student.exam-student-mark';

    protected static ?string $navigationLabel = 'Data Nilai';

    protected static ?string $modelLabel = 'Data Nilai';

    protected static ?string $title = 'Data Nilai';

    public $record;

    public function mount($record) {
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ExamStudentExamStudent::query()->where('exam_schedule_id', $this->record))
            ->columns([
                TextColumn::make('index')
                ->rowIndex()
                ->label('No.')
                ->alignCenter()
                ->width('10px'),
                TextColumn::make('user.name')
                ->label('Nama')
                ->searchable()
                ->sortable(),
                TextColumn::make('mark')
                ->label('Nilai')
                ->searchable()
            ]);
    }
}
