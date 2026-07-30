<div class="space-y-4">
    <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm sm:grid-cols-3">
        <div class="flex justify-between border-b border-gray-100 py-1 dark:border-white/10">
            <span class="text-gray-500 dark:text-gray-400">Tahun Anggaran</span>
            <span class="font-medium text-gray-950 dark:text-white">{{ $tahunAnggaran }}</span>
        </div>
        <div class="flex justify-between border-b border-gray-100 py-1 dark:border-white/10">
            <span class="text-gray-500 dark:text-gray-400">Organisasi</span>
            <span class="font-medium text-gray-950 dark:text-white">{{ $organisasi }}</span>
        </div>
        <div class="flex justify-between border-b border-gray-100 py-1 dark:border-white/10">
            <span class="text-gray-500 dark:text-gray-400">Sandi</span>
            <span class="font-medium text-gray-950 dark:text-white">{{ $sandi }}</span>
        </div>
        <div class="flex justify-between border-b border-gray-100 py-1 dark:border-white/10">
            <span class="text-gray-500 dark:text-gray-400">PON</span>
            <span class="font-medium text-gray-950 dark:text-white">{{ $pon }}</span>
        </div>
        <div class="flex justify-between border-b border-gray-100 py-1 dark:border-white/10">
            <span class="text-gray-500 dark:text-gray-400">No. Kontrak</span>
            <span class="font-medium text-gray-950 dark:text-white">{{ $kontrak }}</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5">
                    @foreach (['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'] as $bulan)
                        <th class="px-2 py-2 text-center text-xs font-semibold text-gray-950 dark:text-white">{{ $bulan }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @for ($i = 0; $i < 12; $i++)
                        <td class="px-1 py-2">
                            <x-filament::input.wrapper>
                                <x-filament::input
                                    type="number"
                                    min="0"
                                    wire:model="insertMonthly.{{ $i }}"
                                    class="text-right"
                                />
                            </x-filament::input.wrapper>
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>
    </div>
</div>