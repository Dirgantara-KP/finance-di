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
    }

    #[On('bukti-gaji-selected')]
    public function isiDariBuktiGaji(array $data): void
    {
        $this->data['tgl_proses'] = $data['tanggal_gaji'];
        $this->data['bank_kode'] = $data['bank_kode'];
        $this->data['bank_nama'] = $data['bank_nama'];

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
                    ->schema([
                        // === Nomor Bukti Gaji ===
                        Section::make('Nomor Bukti Gaji')
                            ->columnSpan(7)
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('nomor_bukti')
                                            ->label('Nomor Bukti')
                                            ->disabled()
                                            ->columnSpan(2),
                                        TextInput::make('lok')
                                            ->label('Lok.')
                                            ->disabled(),
                                    ]),
                                DatePicker::make('tgl_proses')
                                    ->label('Tgl Proses')
                                    ->displayFormat('d-m-Y')
                                    ->suffixAction(
                                        Action::make('cariBukti')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->action(fn () => $this->dispatch('open-list-bukti-gaji')),
                                    ),
                            ]),

                        // === Tgl Bukti Media ===
                        Section::make()
                            ->columnSpan(5)
                            ->schema([
                                DatePicker::make('tgl_bukti_media')
                                    ->label('Tgl Bukti Media')
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

                        // === Pembayaran Via ===
                        Section::make()
                            ->columnSpan(12)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Select::make('bank_kode')
                                            ->label('Pembayaran Via')
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
                    ]),

                TextInput::make('uraian_pembayaran')
                    ->label('Uraian Pembayaran')
                    ->columnSpanFull(),

                Grid::make(12)
                    ->schema([
                        Group::make()
                            ->columnSpan(6)
                            ->schema([
                                Fieldset::make('Project No. (PON)')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('pon_no')
                                                    ->maxWidth('full'),

                                                TextInput::make('pon_seq')
                                                    ->maxWidth('full'),

                                                TextInput::make('pon_sub')
                                                    ->maxWidth('full'),

                                                TextInput::make('pon_ket')
                                                    ->maxWidth('full')
                                                    ->suffixAction(
                                                        Action::make('cariPon')
                                                            ->icon('heroicon-o-magnifying-glass')
                                                            ->action(fn () => null),
                                                    ),
                                            ]),
                                    ]),
                                Fieldset::make('Dibayar melalui Rek.')
                                    ->schema([
                                        TextInput::make('rek_bayar_no')
                                            ->label(false),
                                        TextInput::make('rek_bayar_nama')
                                            ->label(false)
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

                        Group::make()
                            ->columnSpan(6)
                            ->schema([
                                Select::make('cara_pembayaran')
                                    ->label('Cara Pembayaran')
                                    ->options([
                                        'TRANSFER' => 'TRANSFER',
                                        'TUNAI' => 'TUNAI',
                                        'GIRO' => 'GIRO',
                                    ]),
                                Fieldset::make('Penanggung Jwb. Gaji')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('pj_kode'),
                                                TextInput::make('pj_nama')
                                                    ->columnSpan(2)
                                                    ->suffixAction(
                                                        Action::make('cariPj')
                                                            ->icon('heroicon-o-magnifying-glass')
                                                            ->action(fn () => null),
                                                    ),
                                            ]),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('rek_no')
                                            ->label('Rekening No. / Val.')
                                            ->columnSpan(2),
                                        Select::make('rek_val')
                                            ->label(false)
                                            ->options([
                                                'IDR' => 'IDR',
                                                'USD' => 'USD',
                                            ]),
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
}
