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

    /*
    |--------------------------------------------------------------------------
    | Service
    |--------------------------------------------------------------------------
    */

    public function boot(CollectingGajiService $service): void
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | Filter Utama
    |--------------------------------------------------------------------------
    */

    public ?string $tanggalProsesGaji = null;

    public ?string $noBuktiGaji = null;

    public ?string $bankKas = null;

    public ?string $lokasi = null;

    /*
    |--------------------------------------------------------------------------
    | Print
    |--------------------------------------------------------------------------
    |
    | State ini tetap dipertahankan karena mungkin masih digunakan
    | oleh bagian lain dari halaman / backend.
    |
    */

    public string $jenisPrint = 'rincian';

    public ?string $otorisatorNIK = null;

    public ?string $otorisatorNama = null;

    public ?string $originatorNIK = null;

    public ?string $originatorNama = null;

    /**
     * @var array<int, array{
     *     nik: string,
     *     nama: string,
     *     jabatan: string
     * }>
     */
    public array $daftarOtorisator = [];

    /**
     * @var array<int, array{
     *     nik: string,
     *     nama: string
     * }>
     */
    public array $daftarOriginator = [];

    /*
    |--------------------------------------------------------------------------
    | Dropdown Options
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Daftar Bukti Gaji
    |--------------------------------------------------------------------------
    */

    /**
     * @var array<int, array{
     *     nomor_bukti: string,
     *     dibayar_via: string,
     *     lokasi: string
     * }>
     */
    public array $daftarBuktiGaji = [];

    /*
    |--------------------------------------------------------------------------
    | Rekap Cost Center
    |--------------------------------------------------------------------------
    */

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $rekapCostCenter = [];

    /**
     * Menandakan apakah SHOW sudah dijalankan.
     */
    public bool $dataLoaded = false;

    /**
     * Menandakan data untuk filter saat ini
     * sudah tersedia di TMEMPSALPAY.
     */
    public bool $dataSudahAda = false;

    /*
    |--------------------------------------------------------------------------
    | State Internal Organisasi
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk kebutuhan UPDATE THEMPSALREF.
    |
    | Field ini tidak perlu ditampilkan sebagai input teknis
    | di halaman utama.
    |
    */

    public ?string $orgEselonInput = null;

    public ?string $orgCurTujuan = null;

    /*
    |--------------------------------------------------------------------------
    | Detail Karyawan / Ri
    |--------------------------------------------------------------------------
    */

    public ?string $costCenterAktif = null;

    public ?string $namaDivisiAktif = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $karyawanRincian = [];

    public float $totalBesarGaji = 0;

    public float $totalPihakLain = 0;

    public float $totalYangBersangkutan = 0;

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | No. Bukti Gaji
    |--------------------------------------------------------------------------
    */

    public function updatedNoBuktiGaji(): void
    {
        if ($this->noBuktiGaji === null || $this->noBuktiGaji === '') {
            return;
        }

        /*
         * No. Bukti Gaji hanya boleh mengandung:
         * - angka
         * - huruf
         * - slash (/)
         *
         * Contoh:
         * 25/03/BG/00001
         */
        $clean = preg_replace(
            '/[^0-9A-Za-z\/]/',
            '',
            $this->noBuktiGaji
        );

        $clean = strtoupper($clean);

        /*
         * Batas panjang mengikuti format No. Bukti Gaji
         * yang digunakan pada halaman.
         */
        $clean = substr($clean, 0, 14);

        if ($clean !== $this->noBuktiGaji) {
            $this->noBuktiGaji = $clean;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Ketika Tanggal Proses Gaji Berubah
    |--------------------------------------------------------------------------
    */

    public function updatedTanggalProsesGaji(): void
    {
        /*
         * Reset seluruh state yang bergantung pada tanggal.
         */
        $this->noBuktiGaji = null;

        $this->bankKas = null;

        $this->lokasi = null;

        $this->rekapCostCenter = [];

        $this->dataLoaded = false;

        $this->dataSudahAda = false;

        /*
         * Reset state internal organisasi.
         */
        $this->orgEselonInput = null;

        $this->orgCurTujuan = null;

        /*
         * Reset detail Ri.
         */
        $this->costCenterAktif = null;

        $this->namaDivisiAktif = null;

        $this->karyawanRincian = [];

        $this->totalBesarGaji = 0;

        $this->totalPihakLain = 0;

        $this->totalYangBersangkutan = 0;

        /*
         * Kalau tanggal dikosongkan,
         * semua dropdown dan daftar bukti dikosongkan.
         */
        if (blank($this->tanggalProsesGaji)) {
            $this->noBuktiOptions = [];

            $this->bankKasOptions = [];

            $this->lokasiOptions = [];

            $this->daftarBuktiGaji = [];

            return;
        }

        /*
         * Dropdown:
         * No Bukti
         * Bank/Kas
         * Lokasi
         *
         * semuanya diambil melalui service.
         */
        $options = $this->service->getDropdownOptions(
            $this->tanggalProsesGaji
        );

        $this->noBuktiOptions = $options['noBukti'];

        $this->bankKasOptions = $options['bankKas'];

        $this->lokasiOptions = $options['lokasi'];

        /*
         * Daftar Bukti Gaji untuk modal.
         *
         * Saat tanggal dipilih, kita ambil seluruh daftar
         * untuk tanggal tersebut terlebih dahulu.
         *
         * Filter Bank/Kas dan Lokasi boleh null.
         */
        $this->daftarBuktiGaji = $this->service
            ->getDaftarBukti(
                tanggalProsesGaji: $this->tanggalProsesGaji,
                bankKas: null,
                lokasi: null,
            )
            ->map(
                fn ($row) => [
                    'nomor_bukti' => trim(
                        (string) $row->NOMOR_BUKTI_GAJI
                    ),

                    'dibayar_via' => trim(
                        (string) $row->DIBAYAR_VIA
                    ),

                    'lokasi' => trim(
                        (string) $row->LOKASI
                    ),
                ]
            )
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function showRekapCostCenter(): void
    {
        /*
         * Validasi tanggal.
         */
        if (blank($this->tanggalProsesGaji)) {
            Notification::make()
                ->warning()
                ->title(
                    'Tanggal Proses Gaji harus dipilih terlebih dahulu.'
                )
                ->send();

            return;
        }

        /*
         * Validasi No. Bukti Gaji.
         */
        if (blank($this->noBuktiGaji)) {
            Notification::make()
                ->warning()
                ->title(
                    'Pilih No. Bukti Gaji terlebih dahulu.'
                )
                ->send();

            return;
        }

        /*
         * Validasi Bank/Kas dan Lokasi.
         */
        if (
            blank($this->bankKas)
            || blank($this->lokasi)
        ) {
            Notification::make()
                ->warning()
                ->title(
                    'Bank/Kas dan Lokasi harus dipilih terlebih dahulu.'
                )
                ->send();

            return;
        }

        /*
         * Ambil rekap.
         *
         * Service yang menentukan:
         *
         * 1. Jika data filter sudah ada di TMEMPSALPAY
         *    -> gunakan TMEMPSALPAY.
         *
         * 2. Jika belum ada
         *    -> fallback ke VEMPSALPAY.
         */
        $result = $this->service->getRekapCostCenter(
            tanggalProsesGaji: $this->tanggalProsesGaji,
            bankKas: $this->bankKas,
            lokasi: $this->lokasi,
            nomorBukti: $this->noBuktiGaji,
        );

        $this->dataSudahAda = $result['sudah_ada'];

        $this->rekapCostCenter = $result['rows'];

        $this->dataLoaded = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Pilih No. Bukti Gaji
    |--------------------------------------------------------------------------
    */

    public function pilihNoBukti(): void
    {
        if (blank($this->tanggalProsesGaji)) {
            Notification::make()
                ->warning()
                ->title(
                    'Tanggal Proses Gaji harus dipilih terlebih dahulu.'
                )
                ->send();

            return;
        }

        /*
         * Ambil daftar bukti berdasarkan:
         *
         * tanggal
         * bank/kas
         * lokasi
         *
         * Kalau Bank/Kas atau Lokasi belum dipilih,
         * service dapat menerima null.
         */
        $this->daftarBuktiGaji = $this->service
            ->getDaftarBukti(
                tanggalProsesGaji: $this->tanggalProsesGaji,
                bankKas: $this->bankKas,
                lokasi: $this->lokasi,
            )
            ->map(
                fn ($row) => [
                    'nomor_bukti' => trim(
                        (string) $row->NOMOR_BUKTI_GAJI
                    ),

                    'dibayar_via' => trim(
                        (string) $row->DIBAYAR_VIA
                    ),

                    'lokasi' => trim(
                        (string) $row->LOKASI
                    ),
                ]
            )
            ->values()
            ->all();

        $this->dispatch(
            'open-modal',
            id: 'daftar-bukti-gaji'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Memilih Baris dari Modal Daftar Bukti Gaji
    |--------------------------------------------------------------------------
    */

    public function pilihBaris(
        string $nomorBukti,
        string $bankKas,
        string $lokasi
    ): void {
        /*
         * Bersihkan whitespace.
         */
        $nomorBukti = trim($nomorBukti);

        /*
         * Bersihkan hanya tanda kutip pembungkus.
         *
         * Tujuannya supaya nilai seperti:
         *
         * '25/03/BG/00001'
         *
         * menjadi:
         *
         * 25/03/BG/00001
         *
         * tanpa mengubah isi nomor bukti.
         */
        $nomorBukti = preg_replace(
            '/^[\'"]+|[\'"]+$/',
            '',
            $nomorBukti
        );

        $bankKas = trim($bankKas);

        $lokasi = trim($lokasi);

        /*
         * Isi filter utama.
         */
        $this->noBuktiGaji = $nomorBukti;

        $this->bankKas = $bankKas;

        $this->lokasi = $lokasi;

        /*
         * Tutup modal.
         */
        $this->dispatch(
            'close-modal',
            id: 'daftar-bukti-gaji'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RI - Lihat Daftar Karyawan
    |--------------------------------------------------------------------------
    */

    public function lihatDaftarKaryawan(int $rowIndex): void
    {
        /*
         * Ambil baris yang diklik.
         */
        $row = $this->rekapCostCenter[$rowIndex] ?? null;

        if (
            ! $row
            || blank($this->tanggalProsesGaji)
        ) {
            return;
        }

        /*
         * Ambil detail karyawan berdasarkan:
         *
         * tanggal proses
         * unit organisasi
         * bank/kas
         * lokasi
         * nomor bukti
         */
        $detail = $this->service->getDetailKaryawan(
            tanggalProsesGaji: $this->tanggalProsesGaji,
            orgCur: $row['org_cur'],
            bankKas: $this->bankKas,
            lokasi: $this->lokasi,
            nomorBukti: $this->noBuktiGaji,
        );

        /*
         * Informasi header modal Ri.
         */
        $this->costCenterAktif = $row['cost_center'];

        $this->namaDivisiAktif = $row['lokasi'];

        /*
         * Detail karyawan.
         */
        $this->karyawanRincian = $detail['rows'];

        /*
         * Total.
         */
        $this->totalBesarGaji = $detail['total_besar_gaji'];

        $this->totalPihakLain = $detail['total_pihak_lain'];

        $this->totalYangBersangkutan =
            $detail['total_yang_bersangkutan'];

        /*
         * Buka modal Ri.
         */
        $this->dispatch(
            'open-modal',
            id: 'daftar-gaji-karyawan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Kembali dari Detail Karyawan
    |--------------------------------------------------------------------------
    */

    public function kembaliKeRekapGaji(): void
    {
        $this->dispatch(
            'close-modal',
            id: 'daftar-gaji-karyawan'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Gaji Non Corporate
    |--------------------------------------------------------------------------
    */

    public function jumlahGajiNonCorporate(): void
    {
        $this->dispatch(
            'open-modal',
            id: 'gaji-non-corporate'
        );
    }

    public function entryRekapGajiManual(): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | PRINT
    |--------------------------------------------------------------------------
    |
    | Method ini dipertahankan dari coding sebelumnya.
    | Kalau tombol Print di Blade dibuat non-functional,
    | method ini tidak akan dipanggil.
    |
    */

    public function print(): void
    {
        if (blank($this->tanggalProsesGaji)) {
            Notification::make()
                ->warning()
                ->title(
                    'Tanggal Proses Gaji harus dipilih terlebih dahulu.'
                )
                ->send();

            return;
        }

        $this->daftarOtorisator =
            $this->service->getOtorisator();

        $this->daftarOriginator =
            $this->service->getOriginator();

        $this->jenisPrint = 'rincian';

        $this->otorisatorNIK = null;

        $this->otorisatorNama = null;

        $this->originatorNIK = null;

        $this->originatorNama = null;

        $this->dispatch(
            'open-modal',
            id: 'print-transaksi-gaji'
        );
    }

    public function pilihOtorisator(
        string $nik,
        string $nama
    ): void {
        $this->otorisatorNIK = $nik;

        $this->otorisatorNama = $nama;
    }

    public function pilihOriginator(
        string $nik,
        string $nama
    ): void {
        $this->originatorNIK = $nik;

        $this->originatorNama = $nama;
    }

    public function konfirmasiCetak(): void
    {
        if (
            blank($this->otorisatorNIK)
            || blank($this->originatorNIK)
        ) {
            Notification::make()
                ->warning()
                ->title(
                    'Pilih Otorisator dan Originator terlebih dahulu.'
                )
                ->body(
                    'Double click baris yang dimaksud.'
                )
                ->send();

            return;
        }

        $this->dispatch(
            'close-modal',
            id: 'print-transaksi-gaji'
        );

        Notification::make()
            ->success()
            ->title(
                'Pilihan cetak dikonfirmasi.'
            )
            ->body(
                'Otorisator: '
                . $this->otorisatorNIK
                . ' - '
                . $this->otorisatorNama
                . ' | Originator: '
                . $this->originatorNIK
                . ' - '
                . $this->originatorNama
                . ' | Jenis: '
                . (
                    $this->jenisPrint === 'rekap'
                        ? 'Rekapitulasi'
                        : 'Rincian per NIK'
                )
            )
            ->send();
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT
    |--------------------------------------------------------------------------
    |
    | INSERT:
    | TMEMPSALPAY
    |
    */

    public function insert(): void
    {
        /*
         * SHOW wajib dilakukan terlebih dahulu.
         */
        if (! $this->dataLoaded) {
            Notification::make()
                ->warning()
                ->title(
                    'Klik Show terlebih dahulu sebelum Insert.'
                )
                ->send();

            return;
        }

        /*
         * Kalau data sudah ada di TMEMPSALPAY,
         * jangan INSERT lagi.
         */
        if ($this->dataSudahAda) {
            Notification::make()
                ->warning()
                ->title(
                    'Data untuk filter ini sudah ada di TMEMPSALPAY.'
                )
                ->body(
                    'Gunakan Update untuk data yang sudah ada.'
                )
                ->send();

            return;
        }

        try {
            $jumlahBaris =
                $this->service->insertCollectingGaji(
                    tanggalProsesGaji: $this->tanggalProsesGaji,
                    iUser: (string) (
                        auth()->user()?->getAuthIdentifier()
                        ?? 'SYSTEM'
                    ),
                );
        } catch (\RuntimeException $e) {
            Notification::make()
                ->danger()
                ->title(
                    $e->getMessage()
                )
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title(
                "Insert berhasil, {$jumlahBaris} baris tersimpan ke TMEMPSALPAY."
            )
            ->send();

        /*
         * Jalankan SHOW kembali.
         *
         * Setelah INSERT:
         *
         * dataSudahAda = true
         *
         * sehingga tabel sekarang membaca TMEMPSALPAY.
         */
        $this->showRekapCostCenter();
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    |
    | UPDATE:
    | THEMPSALREF
    |
    */

    public function update(): void
    {
        /*
         * Update hanya boleh dilakukan setelah SHOW
         * dan data memang sudah ada di TMEMPSALPAY.
         */
        if (
            ! $this->dataLoaded
            || ! $this->dataSudahAda
        ) {
            Notification::make()
                ->warning()
                ->title(
                    'Update hanya untuk data yang sudah ada di TMEMPSALPAY.'
                )
                ->body(
                    'Klik Show dahulu.'
                )
                ->send();

            return;
        }

        /*
         * OrgCur tujuan wajib tersedia.
         */
        if (blank($this->orgCurTujuan)) {
            Notification::make()
                ->warning()
                ->title(
                    'Organisasi tujuan untuk Update belum tersedia.'
                )
                ->send();

            return;
        }

        /*
         * Daftar organisasi/eselon wajib tersedia.
         */
        $orgGaji = $this->parseOrgEselon();

        if (empty($orgGaji)) {
            Notification::make()
                ->warning()
                ->title(
                    'Daftar organisasi/eselon untuk Update belum tersedia.'
                )
                ->send();

            return;
        }

        try {
            $jumlahBaris =
                $this->service->updateThempSalRef(
                    tanggalProsesGaji: $this->tanggalProsesGaji,
                    orgCur: $this->orgCurTujuan,
                    orgGaji: $orgGaji,
                );
        } catch (\RuntimeException $e) {
            Notification::make()
                ->danger()
                ->title(
                    $e->getMessage()
                )
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title(
                "Update berhasil, {$jumlahBaris} baris THEMPSALREF diperbarui."
            )
            ->send();
    }

    /*
    |--------------------------------------------------------------------------
    | Parse Organisasi / Eselon
    |--------------------------------------------------------------------------
    |
    | Contoh input:
    |
    | "01,02,03"
    |
    | atau:
    |
    | "01;02;03"
    |
    | atau:
    |
    | "01 02 03"
    |
    | menjadi:
    |
    | [
    |     "01",
    |     "02",
    |     "03",
    | ]
    |
    */

    private function parseOrgEselon(): array
    {
        if (blank($this->orgEselonInput)) {
            return [];
        }

        $values = preg_split(
            '/[\s,;]+/',
            trim($this->orgEselonInput)
        );

        if ($values === false) {
            return [];
        }

        return collect($values)
            ->map(
                fn ($value) => trim((string) $value)
            )
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    |
    | DELETE:
    | TMEMPSALPAY
    |
    */

    public function delete(): void
    {
        /*
         * Delete hanya boleh dilakukan terhadap data
         * yang memang sudah ada di TMEMPSALPAY.
         */
        if (
            ! $this->dataLoaded
            || ! $this->dataSudahAda
        ) {
            Notification::make()
                ->warning()
                ->title(
                    'Delete hanya untuk data yang sudah ada di TMEMPSALPAY.'
                )
                ->body(
                    'Klik Show dahulu.'
                )
                ->send();

            return;
        }

        try {
            $jumlahBaris =
                $this->service->deleteCollectingGaji(
                    tanggalProsesGaji: $this->tanggalProsesGaji,
                    nomorBukti: $this->noBuktiGaji ?? '%',
                    bankKas: $this->bankKas,
                    lokasi: $this->lokasi,
                );
        } catch (\RuntimeException $e) {
            Notification::make()
                ->danger()
                ->title(
                    $e->getMessage()
                )
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title(
                "Delete berhasil, {$jumlahBaris} baris TMEMPSALPAY dihapus."
            )
            ->send();

        /*
         * SHOW kembali.
         *
         * Setelah DELETE, TMEMPSALPAY tidak lagi mempunyai
         * data untuk filter tersebut.
         *
         * Service akan fallback kembali ke VEMPSALPAY.
         */
        $this->showRekapCostCenter();
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */

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

        $this->orgCurTujuan = null;

        $this->costCenterAktif = null;

        $this->namaDivisiAktif = null;

        $this->karyawanRincian = [];

        $this->totalBesarGaji = 0;

        $this->totalPihakLain = 0;

        $this->totalYangBersangkutan = 0;

        /*
         * Beritahu Blade/Alpine bahwa form sudah di-reset.
         */
        $this->dispatch('form-direset');
    }

    /*
    |--------------------------------------------------------------------------
    | CLOSE
    |--------------------------------------------------------------------------
    */

    public function close(): void
    {
        $this->redirect(
            Filament::getCurrentOrDefaultPanel()->getUrl()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Nama Bulan Indonesia
    |--------------------------------------------------------------------------
    */

    public function namaBulanIndonesia(
        string $tanggal
    ): string {
        $bulanIndonesia = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $carbon = Carbon::parse($tanggal);

        return $bulanIndonesia[
            (int) $carbon->format('n')
        ]
            . ' '
            . $carbon->format('Y');
    }
}