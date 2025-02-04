@php
    $hour = date('H');
    $greeting = '';

    if ($hour >= 5 && $hour < 12) {
        $greeting = 'Pagi';
    } elseif ($hour >= 12 && $hour < 15) {
        $greeting = 'Siang';
    } elseif ($hour >= 15 && $hour < 18) {
        $greeting = 'Sore';
    } else {
        $greeting = 'Malam';
    }
@endphp

<div>
    <span class="text-[10px] text-slate-500 dark:text-slate-400 block leading-[21px]">Hai, Selamat {{ $greeting }}</span>
    <span class="text-[12px] text-slate-900 dark:text-slate-200 block leading-[11px]">{{ Auth::user()->name }} ({{ Str::replace('-', ' ', Str::title(Auth::user()->roles()->first()->name)) }})</span>
</div>
