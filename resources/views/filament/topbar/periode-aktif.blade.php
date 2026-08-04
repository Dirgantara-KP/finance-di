@php
    $bulanIndonesia = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $now = now();
    $periodeAktif = $bulanIndonesia[(int) $now->format('n')] . ' ' . $now->format('Y');
@endphp

<div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
    <span class="font-medium">Periode Aktif :</span>
    <span>{{ $periodeAktif }}</span>
    <x-filament::icon icon="heroicon-o-calendar" class="h-4 w-4" />
</div>