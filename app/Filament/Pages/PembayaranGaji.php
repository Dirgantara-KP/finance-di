<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Attributes\On;
use UnitEnum;

class PembayaranGaji extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static string|UnitEnum|null $navigationGroup = 'Cash Out';

    protected static ?string $navigationLabel = 'Pembayaran Gaji';

    protected static ?string $slug = 'cash-out/pembayaran-gaji';

    protected string $view = 'filament.pages.pembayaran-gaji';

    public ?array $data = [];

    public array $rincianGaji = [];

    public array $daftarBuktiGajiAll = [];

    public array $filterBuktiGaji = [
        'tgl_dari' => '2022-01-01',
        'tgl_sampai' => '2025-12-31',
        'bank' => '',
        'nama_bank' => '',
    ];

    public ?array $selectedBuktiGajiItem = null;

    public int $halamanBuktiGaji = 1;

    protected int $perPageBuktiGaji = 10;

    public function getTitle(): string
    {
        return 'Pembayaran Gaji';
    }

    public function mount(): void
    {
        $this->rincianGaji = [
            ['unit_org' => '1000', 'nama_unit' => 'DIREKTORAT UTAMA', 'via' => 'BCA', 'besar_gaji' => 125000000, 'potongan' => 7500000],
            ['unit_org' => '2000', 'nama_unit' => 'DIREKTORAT KEUANGAN', 'via' => 'BCA', 'besar_gaji' => 98500000, 'potongan' => 5910000],
            ['unit_org' => '3000', 'nama_unit' => 'DIREKTORAT OPERASIONAL', 'via' => 'BCA', 'besar_gaji' => 151750000, 'potongan' => 9105000],
            ['unit_org' => '4000', 'nama_unit' => 'DIREKTORAT SDM', 'via' => 'BCA', 'besar_gaji' => 88250000, 'potongan' => 5295000],
            ['unit_org' => '5000', 'nama_unit' => 'DIREKTORAT PEMASARAN', 'via' => 'BCA', 'besar_gaji' => 110600000, 'potongan' => 6636000],
            ['unit_org' => '6000', 'nama_unit' => 'DIREKTORAT IT', 'via' => 'BCA', 'besar_gaji' => 92300000, 'potongan' => 5538000],
        ];

        $this->form->fill([
            'nomor_bukti' => 'PGJ/FD/2025/05/0001',
            'lok' => '01',
            'tgl_proses' => '2025-05-31',
            'tgl_bukti_media' => '2025-05-31',
            'no_bukti_media_prefix' => 'KU0000',
            'no_bukti_media' => 'BB-2505-00000',
            'bank_kode' => 'BCA',
            'bank_nama' => 'BANK CENTRAL ASIA TBK',
            'uraian_pembayaran' => 'Pembayaran Gaji Karyawan Bulan Mei 2025',
            'pon_no' => 'PGM-25',
            'pon_seq' => '001',
            'pon_sub' => '01',
            'pon_ket' => 'Pembayaran Gaji Mei 2025',
            'rek_bayar_no' => 'BCA - 1234567890',
            'rek_bayar_nama' => 'BANK CENTRAL ASIA TBK',
            'no_giro_cek' => 'BG-0525-00156',
            'cara_pembayaran' => 'TRANSFER',
            'pj_kode' => 'FD01',
            'pj_nama' => 'FINANCE DEPARTMENT',
            'rek_no' => '1234567890',
            'rek_val' => 'IDR',
            'bank_tujuan' => 'BANK CENTRAL ASIA TBK',
        ]);

        $bankList = [
        'BCA' => 'BANK CENTRAL ASIA TBK',
        'BNI' => 'BANK NEGARA INDONESIA (PERSERO) TBK',
        'BRI' => 'BANK RAKYAT INDONESIA (PERSERO) TBK',
        'MANDIRI' => 'BANK MANDIRI (PERSERO) TBK',
    ];
    $bankKodes = array_keys($bankList);

    $this->daftarBuktiGajiAll = [];
    $tanggal = \Carbon\Carbon::create(2025, 5, 31);

    for ($i = 0; $i < 28; $i++) {
        $bankKode = $bankKodes[$i % count($bankKodes)];
        $this->daftarBuktiGajiAll[] = [
            'tgl_gaji'  => $tanggal->copy()->format('Y-m-d'),
            'bank_kode' => $bankKode,
            'bank_nama' => $bankList[$bankKode],
            'jumlah'    => random_int(280, 630) * 10000000,
        ];
        $tanggal->subDays(random_int(1, 16));
    }
        
    }

    #[On('bukti-gaji-selected')]
    public function pilihBuktiGaji(string $tglGaji, string $bankKode, string $bankNama): void
    {
        $this->data['tgl_proses'] = $tglGaji;
        $this->data['bank_kode'] = $bankKode;
        $this->data['bank_nama'] = $bankNama;

        $this->mountedActions = []; 

        Notification::make()
            ->title('Bukti gaji berhasil dipilih')
            ->success()
            ->send();
    }

    public function getJumlahPembayaranProperty(): int
    {
        return collect($this->rincianGaji)->sum(fn (array $row) => $row['besar_gaji'] - $row['potongan']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(12)
                    ->extraAttributes([
                        'class' => 'items-stretch', 
                    ])
                    ->schema([

                        Section::make('Nomor Bukti Gaji')
                            ->columnSpan(7)
                            ->extraAttributes([
                                'class' => 'h-full flex flex-col', 
                            ])
                            ->schema([
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('nomor_bukti')
                                            ->label('Nomor Bukti')
                                            ->disabled()
                                            ->columnSpan(2),

                                        TextInput::make('lok')
                                            ->label('Lok.')
                                            ->disabled()
                                            ->columnSpan(1),

                                        DatePicker::make('tgl_proses')
                                            ->label('Tgl Proses')
                                            ->displayFormat('d-m-Y')
                                            ->columnSpan(2)
                                            ->suffixAction(
                                                Action::make('cariBukti')
                                                    ->icon('heroicon-o-magnifying-glass')
                                                    ->modalHeading('List Bukti Gaji')
                                                    ->modalWidth('4xl')
                                                    ->modalContent(fn () => view('filament.pages.partials.list-bukti-gaji', [
                                                        'hasil' => $this->getBuktiGajiFiltered(),
                                                        'selectedBuktiGajiItem' => $this->selectedBuktiGajiItem,
                                                    ]))
                                                    ->modalSubmitAction(
                                                        Action::make('pilih')
                                                            ->label('Pilih')
                                                            ->icon('heroicon-o-check')
                                                            ->disabled(fn () => blank($this->selectedBuktiGajiItem))
                                                            ->action(function () {
                                                                if (blank($this->selectedBuktiGajiItem)) {
                                                                    return;
                                                                }

                                                                $this->data['tgl_proses'] = $this->selectedBuktiGajiItem['tgl_gaji'];
                                                                $this->data['bank_kode'] = $this->selectedBuktiGajiItem['bank_kode'];
                                                                $this->data['bank_nama'] = $this->selectedBuktiGajiItem['bank_nama'];
                                                                $this->selectedBuktiGajiItem = null;

                                                                Notification::make()->title('Bukti gaji berhasil dipilih')->success()->send();
                                                            }),
                                                    )
                                                    ->modalCancelAction(
                                                        Action::make('tutup')->label('Tutup')->color('gray'),
                                                    ),
                                            ),
                                    ]),

                                Grid::make(4)
                                    ->schema([
                                        Select::make('bank_kode')
                                            ->label('Pembayaran Via')
                                            ->columnSpan(1)
                                            ->options([
                                                'BCA' => 'BCA',
                                                'BNI' => 'BNI',
                                                'BRI' => 'BRI',
                                                'MANDIRI' => 'MANDIRI',
                                            ]),

                                        TextInput::make('bank_nama')
                                            ->label(' ')
                                            ->disabled()
                                            ->columnSpan(3),
                                    ]),
                            ]),

                        Section::make('Tgl Bukti Media')
                            ->columnSpan(5)
                            ->extraAttributes([
                                'class' => 'h-full flex flex-col',
                            ])
                            ->schema([
                                DatePicker::make('tgl_bukti_media')
                                    ->label(' ') 
                                    ->displayFormat('d-m-Y'),
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('no_bukti_media_prefix')
                                            ->label('No. Bukti Media')
                                            ->disabled(),
                                        TextInput::make('no_bukti_media')
                                            ->label(' ')
                                            ->columnSpan(2),
                                    ]),
                            ]),
                    ]),

                TextInput::make('uraian_pembayaran')
                    ->label('Uraian Pembayaran')
                    ->columnSpanFull(),

                Grid::make(12)
                    ->extraAttributes([
                        'class' => 'items-stretch', 
                    ])
                    ->schema([
                        Section::make('Project No. (PON)')
                            ->columnSpan(6)
                            ->extraAttributes([
                                'class' => 'h-full flex flex-col',
                            ])
                            ->schema([

                                Grid::make(2)
                                    ->schema([

                                        TextInput::make('pon_no')
                                            ->label('Program')
                                            ->columnSpan(1),

                                        TextInput::make('pon_seq')
                                            ->label('Sub')
                                            ->columnSpan(1),

                                    ]),

                                Grid::make(4)
                                    ->schema([

                                        TextInput::make('pon_sub')
                                            ->label('Versi')
                                            ->columnSpan(1),

                                        TextInput::make('pon_ket')
                                            ->label('Nama Project')
                                            ->columnSpan(3)
                                            ->suffixAction(
                                                Action::make('cariPon')
                                                    ->icon('heroicon-o-magnifying-glass')
                                                    ->action(fn () => null),
                                            ),

                                    ]),

                                Fieldset::make('Dibayar melalui Rek.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('rek_bayar_no')
                                            ->label('No. Rekening'),

                                        TextInput::make('rek_bayar_nama')
                                            ->label('Nama Rekening')
                                            ->suffixAction(
                                                Action::make('cariRekBayar')
                                                    ->icon('heroicon-o-magnifying-glass')
                                                    ->action(fn () => null),
                                            ),
                                    ]),

                                TextInput::make('no_giro_cek')
                                    ->label('Nomor Giro/Cek')
                                    ->suffixAction(
                                        Action::make('cariGiro')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->action(fn () => null),
                                    ),

                            ]),

                        Section::make('Cara Pembayaran')
                            ->columnSpan(6)
                            ->extraAttributes([
                                'class' => 'h-full flex flex-col', 
                            ])
                            ->schema([

                                Select::make('cara_pembayaran')
                                    ->label('Cara Pembayaran')
                                    ->options([
                                        'TRANSFER' => 'TRANSFER',
                                        'TUNAI' => 'TUNAI',
                                        'GIRO' => 'GIRO',
                                    ]),

                                Fieldset::make('Penanggung Jwb. Gaji')
                                    ->columns(6)
                                    ->schema([
                                        TextInput::make('pj_kode')
                                            ->label('Kode')
                                            ->columnSpan(2),

                                        TextInput::make('pj_nama')
                                            ->label('Nama')
                                            ->columnSpan(4)
                                            ->suffixAction(
                                                Action::make('cariPj')
                                                    ->icon('heroicon-o-magnifying-glass')
                                                    ->action(fn () => null),
                                            ),
                                    ]),

                                Grid::make(3)
                                    ->schema([

                                        TextInput::make('rek_no')
                                            ->label('Rekening No.')
                                            ->columnSpan(2),

                                        TextInput::make('rek_val')
                                            ->label('Val.')
                                            ->disabled()
                                            ->columnSpan(1),

                                    ]),

                                TextInput::make('bank_tujuan')
                                    ->label('Nama Bank Tujuan')
                                    ->suffixAction(
                                        Action::make('cariBankTujuan')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->action(fn () => null),
                                    ),

                            ]),

                    ]),
            ])
    
            ->statePath('data');
    }

    public function print(): void
    {
        Notification::make()->title('Menyiapkan cetakan bukti gaji...')->info()->send();
    }

    public function insert(): void
    {
        $this->form->getState();

        Notification::make()->title('Data pembayaran gaji berhasil ditambahkan')->success()->send();
    }

    public function update(): void
    {
        $this->form->getState();

        Notification::make()->title('Data pembayaran gaji berhasil diperbarui')->success()->send();
    }

    public function delete(): void
    {
        Notification::make()->title('Data pembayaran gaji berhasil dihapus')->danger()->send();
    }

    public function cancel(): void
    {
        $this->mount();

        Notification::make()->title('Perubahan dibatalkan')->warning()->send();
    }

    public function close(): void
    {
        $this->redirect(static::getUrl());
    }

    public function getBuktiGajiFiltered(): array
    {
        $items = collect($this->daftarBuktiGajiAll)
            ->filter(function (array $item) {
                if ($item['tgl_gaji'] < $this->filterBuktiGaji['tgl_dari']
                    || $item['tgl_gaji'] > $this->filterBuktiGaji['tgl_sampai']) {
                    return false;
                }

                if (filled($this->filterBuktiGaji['bank']) && $item['bank_kode'] !== $this->filterBuktiGaji['bank']) {
                    return false;
                }

                if (filled($this->filterBuktiGaji['nama_bank'])
                    && ! str_contains(strtolower($item['bank_nama']), strtolower($this->filterBuktiGaji['nama_bank']))) {
                    return false;
                }

                return true;
            })
            ->sortByDesc('tgl_gaji')
            ->values();

        $total = $items->count();
        $lastPage = max((int) ceil($total / $this->perPageBuktiGaji), 1);

        if ($this->halamanBuktiGaji > $lastPage) {
            $this->halamanBuktiGaji = $lastPage;
        }

        return [
            'items' => $items->forPage($this->halamanBuktiGaji, $this->perPageBuktiGaji)->values()->all(),
            'total' => $total,
            'page' => $this->halamanBuktiGaji,
            'perPage' => $this->perPageBuktiGaji,
            'lastPage' => $lastPage,
        ];
    }

    public function cariBuktiGaji(): void
    {
        $this->halamanBuktiGaji = 1;
        $this->selectedBuktiGajiItem = null;
    }

    public function refreshBuktiGaji(): void
    {
        $this->filterBuktiGaji = [
            'tgl_dari' => '2022-01-01',
            'tgl_sampai' => '2025-12-31',
            'bank' => '',
            'nama_bank' => '',
        ];
        $this->halamanBuktiGaji = 1;
        $this->selectedBuktiGajiItem = null;
    }

    public function gantiHalamanBuktiGaji(int $halaman): void
    {
        $this->halamanBuktiGaji = $halaman;
    }

    public function pilihBarisBuktiGaji(string $tglGaji, string $bankKode, string $bankNama): void
    {
        $this->selectedBuktiGajiItem = [
            'tgl_gaji' => $tglGaji,
            'bank_kode' => $bankKode,
            'bank_nama' => $bankNama,
        ];
    }

    public function pilihLangsungBuktiGaji(string $tglGaji, string $bankKode, string $bankNama): void
    {
        $this->data['tgl_proses'] = $tglGaji;
        $this->data['bank_kode'] = $bankKode;
        $this->data['bank_nama'] = $bankNama;
        $this->selectedBuktiGajiItem = null;
        $this->mountedActions = [];

        Notification::make()->title('Bukti gaji berhasil dipilih')->success()->send();
    }
}
