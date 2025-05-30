<?php

namespace App\Filament\Resources\Master\SubjectResource\Pages;

use App\Filament\Resources\Master\SubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSubjects extends ManageRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Mata Pelajaran')
            ->modalWidth('lg'),
        ];
    }
}
