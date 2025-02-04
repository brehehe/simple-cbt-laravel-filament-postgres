<?php

namespace App\Filament\Resources\Question\QuestionResource\Pages;

use App\Filament\Resources\Question\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Soal'),
        ];
    }
}
