<x-filament-panels::page>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
        <div class="p-6">
            <div class="flex flex-wrap gap-4">
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tahun Anggaran <span class="text-danger-500">*</span>
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="tahunAnggaran">
                            <option value="">-- Pilih Tahun --</option>
                            @foreach($this->tahunOptions as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Organisasi <span class="text-danger-500">*</span>
                    </label>
                    <x-filament::input.wrapper :disabled="!$this->tahunAnggaran">
                        <x-filament::input.select wire:model.live="organisasi" :disabled="!$this->tahunAnggaran">
                            <option value="">-- Pilih Organisasi --</option>
                            @foreach($this->organisasiOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sandi <span class="text-danger-500">*</span>
                    </label>
                    <x-filament::input.wrapper :disabled="!$this->organisasi">
                        <x-filament::input.select wire:model.live="sandi" :disabled="!$this->organisasi">
                            <option value="">-- Pilih Sandi --</option>
                            @foreach($this->sandiOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        PON <span class="text-danger-500">*</span>
                    </label>
                    <x-filament::input.wrapper :disabled="!$this->sandi">
                        <x-filament::input.select wire:model.live="pon" :disabled="!$this->sandi">
                            <option value="">-- Pilih PON --</option>
                            @foreach($this->ponOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        No. Kontrak <span class="text-danger-500">*</span>
                    </label>
                    <x-filament::input.wrapper :disabled="!$this->pon">
                        <x-filament::input.select wire:model.live="kontrak" :disabled="!$this->pon">
                            <option value="">-- Pilih Kontrak --</option>
                            @foreach($this->kontrakOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>

            <div class="mt-4">
                <x-filament::button
                    wire:click="muatData"
                    icon="heroicon-m-magnifying-glass"
                    :disabled="! $this->allFiltersSelected">
                    Muat Data
                </x-filament::button>
            </div>
        </div>
    </div>

    <div class="mt-6">
        {{ $this->table }}
    </div>

    <div class="flex gap-4 mt-6">
        <x-filament::button
            color="success"
            icon="heroicon-m-document-arrow-down"
            :disabled="! $this->dataLoaded"
            wire:click="exportExcel">
            Export Excel
        </x-filament::button>

        <x-filament::button
            color="primary"
            icon="heroicon-m-plus"
            :disabled="! $this->canInsert"
            wire:click="insert">
            Insert
        </x-filament::button>

        <x-filament::button
            color="warning"
            icon="heroicon-m-pencil-square"
            :disabled="! $this->canUpdate"
            wire:click="update">
            Update
        </x-filament::button>

        <x-filament::button
            color="gray"
            icon="heroicon-m-arrow-uturn-left"
            wire:click="cancel">
            Cancel
        </x-filament::button>

        <x-filament::button
            color="gray"
            icon="heroicon-m-x-mark"
            wire:click="close">
            Close
        </x-filament::button>
    </div>
</x-filament-panels::page>
