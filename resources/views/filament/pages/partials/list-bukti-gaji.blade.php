<div class="space-y-2">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left border-b">
                <th class="py-2 px-2">Tgl Gaji</th>
                <th class="py-2 px-2">Dibayar Via</th>
                <th class="py-2 px-2">Nama Bank</th>
                <th class="py-2 px-2 text-right">Jumlah</th>
                <th class="py-2 px-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="py-2 px-2">{{ \Carbon\Carbon::parse($item['tgl_gaji'])->format('d-m-Y') }}</td>
                    <td class="py-2 px-2">{{ $item['bank_kode'] }}</td>
                    <td class="py-2 px-2">{{ $item['bank_nama'] }}</td>
                    <td class="py-2 px-2 text-right">{{ number_format($item['jumlah'], 0, ',', '.') }}</td>
                    <td class="py-2 px-2 text-right">
                        <x-filament::button
                            size="xs"
                            wire:click="pilihBuktiGaji('{{ $item['tgl_gaji'] }}', '{{ $item['bank_kode'] }}', '{{ $item['bank_nama'] }}')"
                        >
                            Pilih
                        </x-filament::button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>