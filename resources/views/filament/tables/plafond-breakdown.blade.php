@php
    $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $fmt = fn ($v) => number_format($v, 2, ',', '.');

    $monthlySaldo = array_fill(0, 12, 0);
    $monthlyAdd = array_fill(0, 12, 0);

    foreach ($records as $record) {
        for ($i = 1; $i <= 12; $i++) {
            $monthlySaldo[$i - 1] += (float) ($record->{"V_BDGT_SALDOMONTH{$i}"} ?? 0);
            $monthlyAdd[$i - 1] += (float) ($record->{"V_BDGT_ADDMONTH{$i}"} ?? 0);
        }
    }

    $monthlyAkhir = [];
    $totalSaldoAwal = 0;
    $totalPenambahan = 0;
    $totalSaldoAkhir = 0;

    for ($i = 0; $i < 12; $i++) {
        $saldoAkhir = $monthlySaldo[$i] + $monthlyAdd[$i];
        $monthlyAkhir[] = $saldoAkhir;
        $totalSaldoAwal += $monthlySaldo[$i];
        $totalPenambahan += $monthlyAdd[$i];
        $totalSaldoAkhir += $saldoAkhir;
    }
@endphp

@if (count($records) > 0)
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5">
                        <th class="w-0 px-3 py-3.5 text-center text-sm font-semibold text-gray-950 dark:text-white">No</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">Uraian</th>
                        @foreach ($monthLabels as $label)
                            <th class="w-0 whitespace-nowrap px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:text-white">{{ $label }}</th>
                        @endforeach
                        <th class="w-0 whitespace-nowrap border-l border-gray-200 px-3 py-3.5 text-right text-sm font-semibold text-gray-950 dark:border-white/10 dark:text-white">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    <tr>
                        <td class="px-3 py-4 text-center text-sm text-gray-400 dark:text-gray-500">1</td>
                        <td class="px-3 py-4 font-medium text-gray-950 dark:text-white">Saldo Awal</td>
                        @foreach ($monthlySaldo as $val)
                            <td class="whitespace-nowrap px-3 py-4 text-right tabular-nums text-gray-950 dark:text-white">{{ $fmt($val) }}</td>
                        @endforeach
                        <td class="whitespace-nowrap border-l border-gray-200 px-3 py-4 text-right font-semibold tabular-nums text-gray-950 dark:border-white/10 dark:text-white">{{ $fmt($totalSaldoAwal) }}</td>
                    </tr>
                    <tr class="even:bg-gray-50 dark:even:bg-white/5">
                        <td class="px-3 py-4 text-center text-sm text-gray-400 dark:text-gray-500">2</td>
                        <td class="px-3 py-4 font-medium text-gray-950 dark:text-white">Penambahan</td>
                        @foreach ($monthlyAdd as $val)
                            <td class="whitespace-nowrap px-3 py-4 text-right tabular-nums text-gray-950 dark:text-white">{{ $fmt($val) }}</td>
                        @endforeach
                        <td class="whitespace-nowrap border-l border-gray-200 px-3 py-4 text-right font-semibold tabular-nums text-gray-950 dark:border-white/10 dark:text-white">{{ $fmt($totalPenambahan) }}</td>
                    </tr>
                    <tr>
                        <td class="px-3 py-4 text-center text-sm text-gray-400 dark:text-gray-500">3</td>
                        <td class="px-3 py-4 font-semibold text-gray-950 dark:text-white">Saldo Akhir</td>
                        @foreach ($monthlyAkhir as $val)
                            <td class="whitespace-nowrap px-3 py-4 text-right font-semibold tabular-nums text-gray-950 dark:text-white">{{ $fmt($val) }}</td>
                        @endforeach
                        <td class="whitespace-nowrap border-l border-gray-200 px-3 py-4 text-right font-bold tabular-nums text-gray-950 dark:border-white/10 dark:text-white">{{ $fmt($totalSaldoAkhir) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@else
    <x-filament::empty-state
        icon="heroicon-o-document-text"
        heading="Tidak Ada Data"
        description="Belum ada data plafond anggaran yang tersedia."
    />
@endif
