<?php

namespace App\Filament\Resources\PlafondAnggarans\Pages;

use App\Filament\Resources\PlafondAnggarans\PlafondAnggaranResource;
use App\Models\TmContr;
use Filament\Resources\Pages\CreateRecord;

class CreatePlafondAnggaran extends CreateRecord
{
    protected static string $resource = PlafondAnggaranResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tahunAnggaran = request('tahunAnggaran', date('Y'));
        $organisasi = request('organisasi', 'CO');
        $sandi = request('sandi', '-');
        $pon = request('pon', '-');
        $kontrak = request('kontrak');

        $orgContr = 'CO';
        if ($kontrak) {
            $contract = TmContr::whereRaw("C_ORG_CONTR || '-' || I_CONTR = ?", [$kontrak])->first();
            $orgContr = $contract instanceof TmContr ? $contract->C_ORG_CONTR : 'CO';
            $kontrakId = explode('-', $kontrak, 2)[1] ?? $kontrak;
        } else {
            $kontrakId = '000_'.time();
        }

        $data['C_SOURCE'] = 'COL';
        $data['C_ORG_ID'] = $organisasi;
        $data['C_ORG'] = $organisasi;
        $data['C_ORG_CONTR'] = $orgContr;
        $data['I_CONTR'] = $kontrakId;
        $data['C_BDGT_CONTRSTAT'] = 'A3';
        $data['C_BDGT_CONTRINEX'] = 'I';
        $data['C_BDGT_ANGGARAN'] = $tahunAnggaran;
        $data['C_PGM'] = $pon;
        $data['C_PGM_SUB'] = $pon;
        $data['C_PGM_VER'] = $pon;
        $data['C_COA_DR'] = $sandi;
        $data['C_COA_CR'] = 'A23';
        $data['C_CY'] = 'IDR';
        $data['I_ENTRY'] = auth()->user()?->getAuthIdentifier() ?? 'SYSTEM';
        $data['C_ORG_CENTER'] = $organisasi;

        return $this->computeMonthlyFields($data);
    }

    private function computeMonthlyFields(array $data): array
    {
        $addTotal = 0;
        $saldoTotal = 0;

        for ($i = 1; $i <= 12; $i++) {
            $add = (float) ($data["V_BDGT_ADDMONTH{$i}"] ?? 0);
            $data["V_BDGT_ADDMONTH{$i}"] = $add;

            $saldo = $add;
            $data["V_BDGT_SALDOMONTH{$i}"] = $saldo;

            $addTotal += $add;
            $saldoTotal += $saldo;
        }

        $data['V_BDGT_ADDTOTAL'] = $addTotal;
        $data['V_BDGT_SALDOTOTAL'] = $saldoTotal;
        $data['V_BDGT_PLANTOTAL'] = $addTotal;

        return $data;
    }
}
