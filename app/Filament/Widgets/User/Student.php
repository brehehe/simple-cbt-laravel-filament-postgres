<?php

namespace App\Filament\Widgets\User;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class Student extends Widget
{
    protected static string $view = 'filament.widgets.user.student';

    protected int | string | array $columnSpan = 'full';

    public $user;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->hasAnyRole('siswa');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole('siswa');
    }

    public function mount() {
        $this->user = Auth::user();
    }
}
