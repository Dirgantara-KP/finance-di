<?php

namespace App\Services;

use App\Repositories\ThempSalRefRepository;
use App\Repositories\TmempSalPayRepository;
use App\Repositories\VempSalPayEmpRepository;
use App\Repositories\VempSalPayRepository;
use Illuminate\Support\Collection;

final class CollectingGajiService
{
    private const BANK_LABELS = [
        'BBD' => 'BBD - BANK MANDIRI',
        'BNI' => 'BNI - BANK NEGARA INDONESIA',
        'BRI' => 'BRI - BANK RAKYAT INDONESIA',
    ];

    public function __construct(
        private readonly VempSalPayRepository $repo,
        private readonly TmempSalPayRepository $tmemSalPayRepo,
        private readonly VempSalPayEmpRepository $vempSalPayEmpRepo,
        private readonly ThempSalRefRepository $thempSalRefRepo,
    ) {}

    public function getDropdownOptions(
        string $tanggalProsesGaji
    ): array {
        return [
            'noBukti' => $this->repo
                ->findDistinctNoBukti($tanggalProsesGaji),

            'bankKas' => collect(
                $this->repo->findDistinctBankGaji(
                    $tanggalProsesGaji
                )
            )
                ->mapWithKeys(
                    fn ($value) => [
                        $value =>
                            self::BANK_LABELS[$value] ?? $value,
                    ]
                )
                ->all(),

            'lokasi' => collect(
                $this->repo->findDistinctLokasi(
                    $tanggalProsesGaji
                )
            )
                ->mapWithKeys(
                    fn ($value) => [
                        $value => $value,
                    ]
                )
                ->all(),
        ];
    }

    public function getDaftarBukti(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
    ): Collection {
        $periode = substr(
            $tanggalProsesGaji,
            0,
            7
        );

        return $this->repo->findByPeriode(
            periode: $periode,
            bankKas: $bankKas,
            lokasi: $lokasi,
        );
    }

    public function getRekapCostCenter(
        string $tanggalProsesGaji,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): array {
        $sudahAda =
            $this->tmemSalPayRepo->existsForFilter(
                tanggalProsesGaji: $tanggalProsesGaji,
                bankKas: $bankKas,
                lokasi: $lokasi,
                nomorBukti: $nomorBukti,
            );

        if ($sudahAda) {
            $rows =
                $this->tmemSalPayRepo
                    ->findRekapCostCenter(
                        tanggalProsesGaji:
                            $tanggalProsesGaji,
                        bankKas: $bankKas,
                        lokasi: $lokasi,
                        nomorBukti: $nomorBukti,
                    );

            return [
                'sudah_ada' => true,
                'rows' => $rows
                    ->map(fn ($row) => [
                        'org_echl' => $row->org_echl,
                        'org_cur' => $row->org_cur,
                        'cost_center' =>
                            $row->cost_center,
                        'lokasi' => $row->lokasi,
                        'besar_gaji' =>
                            (float) $row->besar_gaji,
                        'pihak_lain' =>
                            (float) $row->pihak_lain,
                        'yang_bersangkutan' =>
                            (float) $row->yang_bersangkutan,
                    ])
                    ->values()
                    ->all(),
            ];
        }

        $rows =
            $this->repo->findRekapCostCenter(
                tanggalProsesGaji:
                    $tanggalProsesGaji,
                nomorGaji: $nomorBukti,
                bankGaji: $bankKas,
                lokasiBayar: $lokasi,
            );

        return [
            'sudah_ada' => false,
            'rows' => $rows
                ->map(fn ($row) => [
                    'org_echl' => $row->org_echl,
                    'org_cur' => $row->org_cur,
                    'cost_center' =>
                        $row->cost_center,
                    'lokasi' => $row->lokasi,
                    'besar_gaji' =>
                        (float) $row->besar_gaji,
                    'pihak_lain' =>
                        (float) $row->pihak_lain,
                    'yang_bersangkutan' =>
                        (float) $row->yang_bersangkutan,
                ])
                ->values()
                ->all(),
        ];
    }

    public function insertCollectingGaji(
        string $tanggalProsesGaji,
        string $nomorBukti,
        string $bankKas,
        string $lokasi,
        string $iUser,
    ): int {
        if (
            $this->tmemSalPayRepo
                ->existsForFilter(
                    tanggalProsesGaji:
                        $tanggalProsesGaji,
                    bankKas: $bankKas,
                    lokasi: $lokasi,
                    nomorBukti: $nomorBukti,
                )
        ) {
            throw new \RuntimeException(
                'Data untuk filter ini sudah pernah di-collecting. Gunakan Update.'
            );
        }

        return $this->tmemSalPayRepo
            ->insertFromVempSalPay(
                periodeGaji: $tanggalProsesGaji,
                nomorBuktiGaji: $nomorBukti,
                bankGaji: $bankKas,
                lokasiBayar: $lokasi,
                iUser: $iUser,
            );
    }

    public function updateThempSalRef(
        string $tanggalProsesGaji,
        string $orgCur,
        array $orgGaji,
    ): int {
        return $this->thempSalRefRepo
            ->updateOrgPayrecpt(
                periodeGaji: $tanggalProsesGaji,
                orgCur: $orgCur,
                orgGaji: $orgGaji,
            );
    }

    public function deleteCollectingGaji(
        string $tanggalProsesGaji,
        string $nomorBukti,
        string $bankKas,
        string $lokasi,
    ): int {
        if (
            ! $this->tmemSalPayRepo
                ->existsForFilter(
                    tanggalProsesGaji:
                        $tanggalProsesGaji,
                    bankKas: $bankKas,
                    lokasi: $lokasi,
                    nomorBukti: $nomorBukti,
                )
        ) {
            throw new \RuntimeException(
                'Data untuk filter ini belum ada di TMEMPSALPAY.'
            );
        }

        return $this->tmemSalPayRepo
            ->deleteByFilter(
                periodeGaji: $tanggalProsesGaji,
                nomorBuktiGaji: $nomorBukti,
                bankGaji: $bankKas,
                lokasiBayar: $lokasi,
            );
    }

    public function getDetailKaryawan(
        string $tanggalProsesGaji,
        string $orgCur,
        ?string $bankKas = null,
        ?string $lokasi = null,
        ?string $nomorBukti = null,
    ): array {
        $rows = $this->vempSalPayEmpRepo
            ->findByCostCenter(
                tanggalProsesGaji:
                    $tanggalProsesGaji,
                orgCur: $orgCur,
                bankKas: $bankKas,
                lokasi: $lokasi,
                nomorBukti: $nomorBukti,
            );

        $mapped = $rows
            ->map(fn ($row) => [
                'nik' => $row->nik,
                'nama' => $row->nama,
                'unit_org' => $row->unit_org,
                'via' => $row->via,
                'lokasi' => $row->lokasi,
                'besar_gaji' =>
                    (float) $row->besar_gaji,
                'pihak_lain' =>
                    (float) $row->pihak_lain,
                'yang_bersangkutan' =>
                    (float) $row->yang_bersangkutan,
            ])
            ->values();

        return [
            'rows' => $mapped->all(),
            'total_besar_gaji' =>
                (float) $mapped->sum('besar_gaji'),
            'total_pihak_lain' =>
                (float) $mapped->sum('pihak_lain'),
            'total_yang_bersangkutan' =>
                (float) $mapped->sum(
                    'yang_bersangkutan'
                ),
        ];
    }
}