<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Locked;

trait FilamentRecordResolver
{
    #[Locked]
    public Model | int | string | null $record;

    protected function resolveRecord(int | string $key): Model
    {
        $record = static::getResource()::resolveRecordRouteBinding($key);
        if ($record === null) throw (new ModelNotFoundException())->setModel($this->getModel(), [$key]);
        return $record;
    }
}
