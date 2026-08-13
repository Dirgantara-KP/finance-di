<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListBuktiGaji extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public bool $isOpen = false;

    public ?string $tanggalDari = null;

    public ?string $tanggalSampai = null;

    public ?string $bankFilter = null;

    public ?string $namaBankCari = null;

    public ?int $selectedId = null;

    protected int $perPage = 10;

    public function mount(): void
    {
        $this->tanggalDari = now()->startOfYear()->subYears(3)->format('Y-m-d');
        $this->tanggalSampai = now()->endOfYear()->format('Y-m-d');
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['tanggalDari', 'tanggalSampai', 'bankFilter', 'namaBankCari'])) {
            $this->resetPage();
        }
    }

    #[On('open-list-bukti-gaji')]
    public function open(): void
    {
        $this->selectedId = null;
        $this->resetPage();
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->selectedId = null;
    }

    public function refreshData(): void
    {
        $this->resetPage();
    }

    public function cari(): void
    {
        $this->resetPage();
    }

    public function pilihBaris(int $id): void
    {
        $this->selectedId = ($this->selectedId === $id) ? null : $id;
    }

    public function konfirmasiPilih(?int $id = null): void
    {
        $id ??= $this->selectedId;

        if (! $id) {
            return;
        }

        $record = collect($this->dummyData())->firstWhere('id', $id);

        if (! $record) {
            return;
        }

        $this->dispatch('bukti-gaji-selected', [
            'tanggal_gaji' => $record['tanggal_gaji'],
            'bank_kode' => $record['bank_kode'],
            'bank_nama' => $record['bank_nama'],
            'jumlah' => $record['jumlah'],
        ]);

        $this->close();
    }

    public function pilihLangsung(int $id): void
    {
        $this->konfirmasiPilih($id);
    }

    protected function dummyData(): array
    {
        $banks = [
            'BCA' => 'BANK CENTRAL ASIA TBK',
            'BNI' => 'BANK NEGARA INDONESIA (PERSERO) TBK',
            'BRI' => 'BANK RAKYAT INDONESIA (PERSERO) TBK',
            'MANDIRI' => 'BANK MANDIRI (PERSERO) TBK',
        ];

        $rows = [];
        $id = 1;
        $date = now()->subMonths(27)->endOfMonth();

        foreach (range(0, 27) as $i) {
            $kodeBank = array_keys($banks)[$i % count($banks)];

            $rows[] = [
                'id' => $id++,
                'tanggal_gaji' => $date->copy()->addMonths($i)->format('Y-m-d'),
                'bank_kode' => $kodeBank,
                'bank_nama' => $banks[$kodeBank],
                'jumlah' => random_int(2_500_000, 6_500_000) * 1000,
            ];
        }

        return collect($rows)->sortByDesc('tanggal_gaji')->values()->all();
    }

    protected function filteredData(): Collection
    {
        return collect($this->dummyData())
            ->when($this->tanggalDari, fn (Collection $c) => $c->filter(
                fn (array $r) => $r['tanggal_gaji'] >= $this->tanggalDari
            ))
            ->when($this->tanggalSampai, fn (Collection $c) => $c->filter(
                fn (array $r) => $r['tanggal_gaji'] <= $this->tanggalSampai
            ))
            ->when($this->bankFilter, fn (Collection $c) => $c->filter(
                fn (array $r) => $r['bank_kode'] === $this->bankFilter
            ))
            ->when($this->namaBankCari, fn (Collection $c) => $c->filter(
                fn (array $r) => str_contains(strtolower($r['bank_nama']), strtolower($this->namaBankCari))
            ))
            ->values();
    }

    /**
     * @return LengthAwarePaginator<<missing>,<missing>>
     */
    public function getRecordsProperty(): LengthAwarePaginator
    {
        $items = $this->filteredData();
        $page = $this->getPage();

        $slice = $items->slice(($page - 1) * $this->perPage, $this->perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $items->count(),
            $this->perPage,
            $page,
            ['pageName' => 'page'],
        );
    }

    /**
     * @return View
     */
    public function render()
    {
        return view('filament.modals.pembayaran-gaji.list-bukti-gaji');
    }
}
