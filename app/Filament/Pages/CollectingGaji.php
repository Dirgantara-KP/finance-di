<?php

namespace App\Filament\Pages;

use App\Services\CollectingGajiService;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;

class CollectingGaji extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Collecting Gaji';

    protected static ?string $title = 'Proses Gaji / Collecting Data Gaji';

    protected string $view = 'filament.pages.collecting-gaji';

    private CollectingGajiService $service;

    public function boot(CollectingGajiService $service): void
    {
        $this->service = $service;
    }

    public ?string $tanggalProsesGaji = null;

    public ?string $noBuktiGaji = null;

    public ?string $bankKas = null;

    public ?string $lokasi = null;

    public string $jenisPrint = 'rincian';

    public ?string $otorisatorNIK = null;

    public ?string $otorisatorNama = null;

    public ?string $originatorNIK = null;

    public ?string $originatorNama = null;

    /**
     * @var array<int, string>
     */
    public array $noBuktiOptions = [];

    /**
     * @var array<string, string>
     */
    public array $bankKasOptions = [];

    /**
     * @var array<string, string>
     */
    public array $lokasiOptions = [];

    /**
     * @var array<int, array{nomor_bukti: string, dibayar_via: string, lokasi: string}>
     */
    public array $daftarBuktiGaji = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $rekapCostCenter = [];

    public bool $dataLoaded = false;

    /**
     * Hasil cek SHOW: apakah kombinasi filter yang aktif SUDAH ada di
     * TMEMPSALPAY. true -> Insert disabled, Update enabled.
     * false -> Insert enabled, Update disabled.
     */
    public bool $dataSudahAda = false;

    /**
     * Daftar kode eselon (2 digit), dipisah koma. Dipakai untuk mempersempit
     * SHOW/INSERT (opsional) dan sebagai :OrgGaji pada query UPDATE (wajib).
     * TODO(April): ganti jadi multi-select/dropdown eselon yang sebenarnya,
     * ini masih input teks sementara supaya alur Insert/Update bisa diuji.
     */
    public ?string $orgEselonInput = null;

    /**
     * :OrgID pada query INSERT FD (kolom c_org_id di TMEMPSALPAY).
     * TODO(April): sumber nilai ini belum ditentukan (Keycloak/org mapping
     * user belum terhubung ke project) — untuk sementara diisi manual.
     */
    public ?string $organisasiPemroses = null;

    /**
     * :OrgCur pada query UPDATE FD (target c_org_payrecpt di THEMPSALREF).
     */
    public ?string $orgCurTujuan = null;

    public ?string $costCenterAktif = null;

    public ?string $namaDivisiAktif = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $karyawanRincian = [];

    public float $totalBesarGaji = 0;

    public float $totalPihakLain = 0;

    public float $totalYangBersangkutan = 0;

    public function mount(): void
    {
        //
    }

    public function updatedNoBuktiGaji(): void
    {
        if ($this->noBuktiGaji === null || $this->noBuktiGaji === '') {
            return;
        }

        $clean = preg_replace('/[^0-9A-Za-z\/]/', '', $this->noBuktiGaji);
        $clean = strtoupper($clean);
        $clean = substr($clean, 0, 14); // panjang maksimal sesuai format YY/MM/BG/xxxxx

        if ($clean !== $this->noBuktiGaji) {
            $this->noBuktiGaji = $clean;
        }
    }

    public function updatedTanggalProsesGaji(): void
    {
        $this->noBuktiGaji = null;
        $this->bankKas = null;
        $this->lokasi = null;
        $this->rekapCostCenter = [];
        $this->dataLoaded = false;

        if (blank($this->tanggalProsesGaji)) {
            $this->noBuktiOptions = [];
            $this->bankKasOptions = [];
            $this->lokasiOptions = [];
            $this->daftarBuktiGaji = [];

            return;
        }

        $periode = substr($this->tanggalProsesGaji, 0, 7);

        $options = $this->service->getDropdownOptions($periode);

        $this->noBuktiOptions = $options['noBukti'];
        $this->bankKasOptions = $options['bankKas'];
        $this->lokasiOptions = $options['lokasi'];

        $this->daftarBuktiGaji = $this->service
            ->getDaftarBukti($periode)
            ->map(fn ($row) => [
                'nomor_bukti' => $row->NOMOR_BUKTI_GAJI,
                'dibayar_via' => $row->DIBAYAR_VIA,
                'lokasi' => $row->LOKASI,
            ])
            ->values()
            ->all();
    }

    public function showRekapCostCenter(): void
    {
        if (blank($this->tanggalProsesGaji)) {
            Notification::make()
                ->warning()
                ->title('Tanggal Proses Gaji harus dipilih terlebih dahulu.')
                ->send();

            return;
        }

        if (blank($this->bankKas) || blank($this->lokasi)) {
            Notification::make()
                ->warning()
                ->title('Bank/Kas dan Lokasi harus dipilih terlebih dahulu.')
                ->send();

            return;
        }

        $hasil = $this->service->getRekapCostCenter(
            tanggalProsesGaji: $this->tanggalProsesGaji,
            bankKas: $this->bankKas,
            lokasi: $this->lokasi,
            nomorBukti: $this->noBuktiGaji,
            orgEselon: $this->parseOrgEselon(),
        );

        $this->rekapCostCenter = $hasil['rows'];
        $this->dataSudahAda = $hasil['sudah_ada'];
        $this->dataLoaded = true;
    }

    /**
     * @return array<int, string>|null
     */
    private function parseOrgEselon(): ?array
    {
        if (blank($this->orgEselonInput)) {
            return null;
        }

        return collect(explode(',', $this->orgEselonInput))
            ->map(fn ($v) => strtoupper(trim($v)))
            ->filter()
            ->values()
            ->all();
    }

    public function pilihNoBukti(): void
    {
        if (blank($this->tanggalProsesGaji)) {
            Notification::make()
                ->warning()
                ->title('Tanggal Proses Gaji harus dipilih terlebih dahulu.')
                ->send();

            return;
        }

        $this->dispatch('open-modal', id: 'daftar-bukti-gaji');
    }

    public function pilihBaris(string $nomorBukti, string $bankKas, string $lokasi): void
    {

        $nomorBukti = trim($nomorBukti);
        $nomorBukti = preg_replace('/^[\'"]+|[\'"]+$/', '', $nomorBukti);

        $this->noBuktiGaji = $nomorBukti;
        $this->bankKas = $bankKas;
        $this->lokasi = $lokasi;

        $this->dispatch(
            'bukti-terpilih',
            noBuktiGaji: $nomorBukti,
            bankKas: $bankKas,
            lokasi: $lokasi,
        );

        $this->dispatch('close-modal', id: 'daftar-bukti-gaji');
    }

    public function lihatDaftarKaryawan(int $rowIndex): void
    {
        $row = $this->rekapCostCenter[$rowIndex] ?? null;

        if (! $row || blank($this->tanggalProsesGaji)) {
            return;
        }

        $detail = $this->service->getDetailKaryawan(
            tanggalProsesGaji: $this->tanggalProsesGaji,
            orgCur: $row['org_cur'],
            bankKas: $this->bankKas,
            lokasi: $this->lokasi,
            nomorBukti: $this->noBuktiGaji,
        );

        $this->costCenterAktif = $row['cost_center'];
        $this->namaDivisiAktif = $row['lokasi'];
        $this->karyawanRincian = $detail['rows'];
        $this->totalBesarGaji = $detail['total_besar_gaji'];
        $this->totalPihakLain = $detail['total_pihak_lain'];
        $this->totalYangBersangkutan = $detail['total_yang_bersangkutan'];

        $this->dispatch('open-modal', id: 'daftar-gaji-karyawan');
    }

    public function kembaliKeRekapGaji(): void
    {
        $this->dispatch('close-modal', id: 'daftar-gaji-karyawan');
    }

    public function jumlahGajiNonCorporate(): void
    {
        $this->dispatch('open-modal', id: 'gaji-non-corporate');
    }

    public function entryRekapGajiManual(): void
    {
        //
    }

    public function print(): void
    {
        if (blank($this->tanggalProsesGaji)) {
            Notification::make()
                ->warning()
                ->title('Tanggal Proses Gaji harus dipilih terlebih dahulu.')
                ->send();

            return;
        }

        $this->dispatch('open-modal', id: 'print-transaksi-gaji');
    }

    public function insert(): void
    {
        if (! $this->dataLoaded) {
            Notification::make()->warning()->title('Klik Show terlebih dahulu sebelum Insert.')->send();

            return;
        }

        if ($this->dataSudahAda) {
            Notification::make()->warning()->title('Data untuk filter ini sudah ada di TMEMPSALPAY, gunakan Update.')->send();

            return;
        }

        if (blank($this->organisasiPemroses)) {
            Notification::make()
                ->warning()
                ->title('Organisasi/eselon pemroses (c_org_id) belum diisi.')
                ->body('Field ini sementara diisi manual — perlu diganti sumber resminya (mis. dari data user login) begitu mapping tersedia.')
                ->send();

            return;
        }

        try {
            $jumlahBaris = $this->service->insertCollectingGaji(
                tanggalProsesGaji: $this->tanggalProsesGaji,
                nomorBukti: $this->noBuktiGaji ?? '%',
                bankKas: $this->bankKas,
                lokasi: $this->lokasi,
                orgId: $this->organisasiPemroses,
                iUser: (string) (auth()->user()?->getAuthIdentifier() ?? 'SYSTEM'),
                orgEselon: $this->parseOrgEselon(),
            );
        } catch (\RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();

            return;
        }

        Notification::make()
            ->success()
            ->title("Insert berhasil, {$jumlahBaris} baris tersimpan ke TMEMPSALPAY.")
            ->send();

        // Muat ulang SHOW supaya tabel Rekap Cost Center sekarang membaca
        // dari TMEMPSALPAY dan tombol Insert/Update ganti state.
        $this->showRekapCostCenter();
    }

    public function update(): void
    {
        if (! $this->dataLoaded || ! $this->dataSudahAda) {
            Notification::make()->warning()->title('Update hanya untuk data yang sudah ada di TMEMPSALPAY. Klik Show dahulu.')->send();

            return;
        }

        if (blank($this->orgCurTujuan) || blank($this->orgEselonInput)) {
            Notification::make()
                ->warning()
                ->title('Organisasi tujuan (:OrgCur) dan daftar eselon (:OrgGaji) wajib diisi untuk Update.')
                ->send();

            return;
        }

        $jumlahBaris = $this->service->updateThempSalRef(
            tanggalProsesGaji: $this->tanggalProsesGaji,
            orgCur: $this->orgCurTujuan,
            orgGaji: $this->parseOrgEselon() ?? [],
        );

        Notification::make()
            ->success()
            ->title("Update berhasil, {$jumlahBaris} baris THEMPSALREF diperbarui.")
            ->send();
    }

    public function delete(): void
    {
        // TODO: menunggu konfirmasi senior soal sumber nilai :organisasi_pemroses
        // (c_org_id) untuk query DELETE FROM TMEMPSALPAY. Jangan diimplementasikan
        // dengan asumsi sebelum konfirmasi didapat.
        Notification::make()
            ->warning()
            ->title('Fitur Delete belum diaktifkan — masih menunggu konfirmasi senior.')
            ->send();
    }

    public function cancel(): void
    {
        $this->tanggalProsesGaji = null;
        $this->noBuktiGaji = null;
        $this->bankKas = null;
        $this->lokasi = null;
        $this->noBuktiOptions = [];
        $this->bankKasOptions = [];
        $this->lokasiOptions = [];
        $this->daftarBuktiGaji = [];
        $this->rekapCostCenter = [];
        $this->dataLoaded = false;
        $this->dataSudahAda = false;
        $this->orgEselonInput = null;
        $this->organisasiPemroses = null;
        $this->orgCurTujuan = null;
        $this->costCenterAktif = null;
        $this->namaDivisiAktif = null;
        $this->karyawanRincian = [];
        $this->totalBesarGaji = 0;
        $this->totalPihakLain = 0;
        $this->totalYangBersangkutan = 0;
    }

    public function close(): void
    {
        $this->redirect(Filament::getCurrentOrDefaultPanel()->getUrl());
    }

    public function namaBulanIndonesia(string $tanggal): string
    {
        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $carbon = Carbon::parse($tanggal);

        return $bulanIndonesia[(int) $carbon->format('n')].' '.$carbon->format('Y');
    }
}