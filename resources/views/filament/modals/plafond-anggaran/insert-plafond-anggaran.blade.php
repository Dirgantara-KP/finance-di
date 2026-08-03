<div class="space-y-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tahun Anggaran
            </label>
            <div class="text-sm font-medium text-gray-950 dark:text-white">{{ $tahunAnggaran }}</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Organisasi <span class="text-red-500">*</span>
            </label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="insertOrg">
                    <option value="">-- Pilih Organisasi --</option>
                    @foreach ($organisasiOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Sandi <span class="text-red-500">*</span>
            </label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="insertSandi">
                    <option value="">-- Pilih Sandi --</option>
                    @foreach ($sandiOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                PON <span class="text-red-500">*</span>
            </label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="insertPon">
                    <option value="">-- Pilih PON --</option>
                    @foreach ($ponOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                No. Kontrak <span class="text-red-500">*</span>
            </label>
            <x-filament::input.wrapper :disabled="! $insertOrg">
                <x-filament::input.select wire:model.live="insertKontrak" :disabled="! $insertOrg">
                    <option value="">-- Pilih Kontrak --</option>
                    @foreach ($insertKontrakOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
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
                            <x-filament::input.index
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