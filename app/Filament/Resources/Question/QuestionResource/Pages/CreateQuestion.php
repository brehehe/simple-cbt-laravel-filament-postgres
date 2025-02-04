<?php

namespace App\Filament\Resources\Question\QuestionResource\Pages;

use App\Filament\Resources\Question\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;
}
