<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CollectingGaji extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Collecting Gaji';

    protected static ?string $title = 'Proses Gaji / Collecting Data Gaji';

    protected string $view = 'filament.pages.collecting-gaji';

    /**
     * TODO(backend): field ini akan disinkronkan dengan tabel/relasi
     * Collecting Gaji setelah model & migration tersedia dari tim backend.
     */
    public ?string $tanggalProsesGaji = null;

    public ?string $noBuktiGaji = null;

    public ?string $bankKas = null;

    public ?string $lokasi = null;

    /**
     * TODO(backend): isi dari master data Bank/Kas.
     *
     * @var array<string, string>
     */
    public array $bankKasOptions = [];

    /**
     * TODO(backend): isi dari master data Lokasi.
     *
     * @var array<string, string>
     */
    public array $lokasiOptions = [];

    /**
     * Baris rekap gaji per Cost Center.
     * Sengaja dikosongkan pada Tahap 1 (belum ada sumber data backend).
     *
     * @var array<int, array<string, mixed>>
     */
    public array $rekapCostCenter = [];

    public bool $dataLoaded = false;

    public function mount(): void
    {
        //
    }

    /**
     * Membuka pencarian Nomor Bukti Gaji yang sudah tersedia.
     * TODO(backend): hubungkan ke query pencarian nomor bukti.
     */
    public function cariNoBukti(): void
    {
        //
    }

    /**
     * Menampilkan daftar Nomor Bukti Gaji yang dapat dipilih.
     * TODO(backend): hubungkan ke daftar nomor bukti dari database.
     */
    public function pilihNoBukti(): void
    {
        //
    }

    /**
     * Menampilkan rekap gaji berdasarkan unit organisasi/eselon (pop-up).
     * TODO(backend): hubungkan ke query rekap per unit organisasi/eselon.
     */
    public function showRekapPerUnit(): void
    {
        $this->dispatch('open-modal', id: 'rekap-per-unit');
    }

    /**
     * Menampilkan daftar gaji karyawan (rincian) untuk satu baris cost center.
     * TODO(backend): hubungkan ke query daftar gaji karyawan.
     */
    public function lihatDaftarKaryawan(int $rowIndex): void
    {
        $this->dispatch('open-modal', id: 'daftar-gaji-karyawan');
    }

    /**
     * Menampilkan tabel rekap gaji non-corporate.
     * TODO(backend): hubungkan ke query gaji non-corporate.
     */
    public function jumlahGajiNonCorporate(): void
    {
        $this->dispatch('open-modal', id: 'gaji-non-corporate');
    }

    /**
     * Membuka form Entry Manual Gaji/Lembur (TRSEntrGajiF).
     * TODO(backend): arahkan ke halaman/route Entry Rekap Gaji Manual.
     */
    public function entryRekapGajiManual(): void
    {
        //
    }

    /**
     * Membuka form konfirmasi cetak (FrmTransGaji).
     * TODO(backend): hubungkan ke proses cetak.
     */
    public function print(): void
    {
        //
    }

    /**
     * Menyimpan data collecting gaji baru.
     * TODO(backend): hubungkan ke proses insert.
     */
    public function insert(): void
    {
        //
    }

    /**
     * Memperbarui data collecting gaji yang sudah tersedia.
     * TODO(backend): hubungkan ke proses update.
     */
    public function update(): void
    {
        //
    }

    /**
     * Menghapus data collecting gaji setelah konfirmasi.
     * TODO(backend): hubungkan ke proses delete + dialog konfirmasi.
     */
    public function delete(): void
    {
        //
    }

    /**
     * Membatalkan proses dan mengembalikan form ke kondisi awal.
     */
    public function cancel(): void
    {
        $this->tanggalProsesGaji = null;
        $this->noBuktiGaji = null;
        $this->bankKas = null;
        $this->lokasi = null;
        $this->rekapCostCenter = [];
        $this->dataLoaded = false;
    }

    /**
     * Menutup halaman Proses Gaji dan kembali ke halaman sebelumnya.
     */
    public function close(): void
    {
        $this->redirect(Filament::getCurrentOrDefaultPanel()->getUrl());
    }
}
