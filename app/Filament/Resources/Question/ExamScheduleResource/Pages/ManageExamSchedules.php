<?php

namespace App\Filament\Resources\Question\ExamScheduleResource\Pages;

use App\Filament\Resources\Question\ExamScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageExamSchedules extends ManageRecords
{
    protected static string $resource = ExamScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Jadwal'),
        ];
    }
}
