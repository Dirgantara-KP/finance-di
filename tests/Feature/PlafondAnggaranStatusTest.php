<?php

use App\Exceptions\ForbiddenActionException;
use App\Models\TmbdgtPlafond;
use App\Models\Tmcontr;
use App\Models\Trchartacct;
use App\Models\Vororg;
use App\Models\Vpon;
use App\Services\PlafondAnggaranService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Trchartacct::query()->create([
        'c_cost_bsis' => '69',
        'c_cost_acctgrp' => 6,
        'c_cost_acctsub' => null,
        'c_cost_acctsubgrp' => 9,
        'c_cost' => '69A',
        'e_cost' => 'Biaya Operasional',
    ]);

    Vpon::query()->create([
        'c_pgm' => 'GS',
        'c_pgm_sub' => 'COR',
        'c_pgm_ver' => '902',
        'e_pgm' => 'Operasional Support',
        'c_org_core' => 'CO',
        'c_pgm_veract' => 'OPN',
    ]);

    Vororg::query()->create([
        'i_org' => 'AK0000',
        'i_org_ut' => 1,
        'i_org_dir' => 1,
        'i_org_subdir' => '',
        'i_org_div' => '',
        'i_org_subdiv' => '',
        'i_org_dept' => '',
        'i_org_subdept' => '',
        'i_org_bid' => '',
        'i_org_subbid' => '',
        'i_org_00' => '',
        'c_org_statlvl' => 1,
        'c_org_cur' => 'AK0000',
        'c_org_direktorat' => '',
        'n_org_cur' => 'Unit AK',
    ]);

    Tmcontr::query()->create([
        'i_id_contr' => 1,
        'c_org_contr' => 'AI0000',
        'i_contr' => 'Operasional Thn 2000',
        'i_contr_ref' => '',
        'n_contr_proj' => 'Proyek Operasional',
    ]);
});

test('insert plafond anggaran defaults c_bdgt_stat to OPN', function () {
    $service = app(PlafondAnggaranService::class);
    $currentYear = now()->year;

    $service->insert([
        'tahun' => $currentYear,
        'org' => 'AK0000',
        'sandi' => '69A',
        'pon' => '902',
        'kontrak' => 'Operasional Thn 2000',
        'add_month' => [1000, 2000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    ]);

    $record = TmbdgtPlafond::query()
        ->where('c_bdgt_anggaran', $currentYear)
        ->where('c_coa_dr', '69A')
        ->where('c_pgm_ver', '902')
        ->where('i_contr', 'Operasional Thn 2000')
        ->first();

    expect($record)->not->toBeNull()
        ->and($record->c_bdgt_stat)->toBe('OPN');
});

test('kondisi 1 dan kondisi 2 update saldo awal dan penambahan sesuai analogi 0 -> 700 -> 800', function () {
    $service = app(PlafondAnggaranService::class);
    $currentYear = now()->year;

    // Kondisi 1: Saldo Awal == null (Insert data baru dengan penambahan 700 pada bulan 1)
    $service->insert([
        'tahun' => $currentYear,
        'org' => 'AK0000',
        'sandi' => '69A',
        'pon' => '902',
        'kontrak' => 'Operasional Thn 2000',
        'add_month' => [700, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    ]);

    $record = TmbdgtPlafond::query()
        ->where('c_bdgt_anggaran', $currentYear)
        ->where('c_coa_dr', '69A')
        ->where('c_pgm_ver', '902')
        ->where('i_contr', 'Operasional Thn 2000')
        ->first();

    expect($record)->not->toBeNull()
        ->and($record->v_bdgt_saldomonth1)->toBe(700)
        ->and($record->v_bdgt_addmonth1)->toBe(700)
        ->and($record->v_bdgt_saldototal)->toBe(700)
        ->and($record->v_bdgt_addtotal)->toBe(700);

    // Kondisi 2: Saldo Awal != null (Update dengan penambahan baru 800 pada bulan 1)
    // Penambahan mereplace nilai 700 menjadi 800, dan Saldo Awal menjadi 700 + 800 = 1500
    $service->update($record->id, [800, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);

    $record->refresh();
    expect($record->v_bdgt_addmonth1)->toBe(800)
        ->and($record->v_bdgt_saldomonth1)->toBe(1500)
        ->and($record->v_bdgt_addtotal)->toBe(800)
        ->and($record->v_bdgt_saldototal)->toBe(1500);

    // Load data kembali di UI: Saldo Awal = 1500, Penambahan = 0, Saldo Akhir = 1500
    $loaded = $service->load([
        'tahun' => $currentYear,
        'org' => 'AK0000',
        'sandi' => '69A',
        'pon' => '902',
        'kontrak' => 'Operasional Thn 2000',
    ]);

    expect($loaded['saldoAwal'][0])->toBe(1500)
        ->and($loaded['addMonth'][0])->toBe(0)
        ->and($loaded['saldoAkhir'][0])->toBe(1500);
});

test('changing status to CLS locks plafond from being updated', function () {
    $service = app(PlafondAnggaranService::class);
    $currentYear = now()->year;

    $record = TmbdgtPlafond::query()->create([
        'c_source' => 'COL',
        'c_org_id' => 'CO',
        'c_org' => 'AK0000',
        'c_org_contr' => 'AI0000',
        'i_contr' => 'Operasional Thn 2000-TEST',
        'c_bdgt_contrstat' => 'A3',
        'c_bdgt_contrinex' => 'I',
        'c_bdgt_anggaran' => $currentYear,
        'c_pgm' => 'GS',
        'c_pgm_sub' => 'COR',
        'c_pgm_ver' => '902',
        'c_coa_dr' => '69A',
        'c_coa_cr' => 'A23',
        'c_cy' => 'IDR',
        'c_bdgt_stat' => 'OPN',
        'v_bdgt_total' => 0,
        'v_bdgt_saldototal' => 0,
        'v_bdgt_plantotal' => 0,
        'v_bdgt_addtotal' => 0,
    ]);

    // Set status ke CLS (Tutup Plafond)
    $service->setStat($record->id, 'CLS');

    $record->refresh();
    expect($record->c_bdgt_stat)->toBe('CLS');

    // Update harus throw ForbiddenActionException
    $service->update($record->id, [500, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
})->throws(ForbiddenActionException::class);
