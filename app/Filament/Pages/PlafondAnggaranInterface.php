<?php

namespace App\Filament\Pages;

interface PlafondAnggaranInterface
{
    public function mount(): void;

    public function resetMonthData(): void;

    public function updatedOrganisasi(): void;

    public function loadKontrakOptions(): void;

    public function updatedTahun(): void;

    public function updatedSandi(): void;

    public function updatedPon(): void;

    public function updatedKontrak(): void;

    public function rules(): array;

    public function loadData(): void;

    public function calculateAll(): void;

    public function updated($property): void;

    public function insert(): void;

    public function update(): void;

    public function cancel(): void;

    public function close(): void;
}
