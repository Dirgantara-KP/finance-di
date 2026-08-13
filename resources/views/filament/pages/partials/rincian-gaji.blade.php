<x-filament::section>
    <x-slot name="heading">Rincian Gaji :</x-slot>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-gray-200 dark:border-white/10">
            <thead class="bg-primary-600 text-white">
                <tr>
                    <th class="px-3 py-2 text-left">No.</th>
                    <th class="px-3 py-2 text-left">Unit Org.</th>
                    <th class="px-3 py-2 text-left">Nama Unit Organisasi</th>
                    <th class="px-3 py-2 text-left">Via / Lok.</th>
                    <th class="px-3 py-2 text-right">Besar Gaji</th>
                    <th class="px-3 py-2 text-right">Potongan / PL</th>
                    <th class="px-3 py-2 text-right">Jumlah Dibayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rincianGaji as $i => $row)
                    <tr class="border-t border-gray-100 dark:border-white/5">
                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                        <td class="px-3 py-2">{{ $row['unit_org'] }}</td>
                        <td class="px-3 py-2">{{ $row['nama_unit'] }}</td>
                        <td class="px-3 py-2">{{ $row['via'] }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row['besar_gaji'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row['potongan'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row['besar_gaji'] - $row['potongan'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-gray-200 dark:border-white/10">
                    <td colspan="6" class="px-3 py-2 text-right font-semibold text-primary-600">
                        Jumlah Pembayaran
                    </td>
                    <td class="px-3 py-2 text-right font-bold text-primary-600">
                        {{ number_format($jumlahPembayaran, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-filament::section>
