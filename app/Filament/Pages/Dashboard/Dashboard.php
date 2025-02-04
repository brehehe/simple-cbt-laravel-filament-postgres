<?php

namespace App\Filament\Pages\Dashboard;

use App\Filament\Widgets\User\Student;
use Filament\Pages\Dashboard as PagesDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends PagesDashboard
{
    protected static ?string $title = 'Dashboard';

    public function getColumns(): int | string | array
    {
        return 12;
    }

    public function getVisibleWidgets(): array
    {
        $details = [];
        $auth = Auth::user();

        if ($auth->hasAnyRole('siswa')) {
            $details = [
                Student::class,
            ];
        }

        return $details;
    }
}
