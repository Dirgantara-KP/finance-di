<?php

namespace App\Filament\Resources\PlafondAnggarans\Pages;

use App\Filament\Resources\PlafondAnggarans\PlafondAnggaranResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPlafondAnggaran extends EditRecord
{
    protected static string $resource = PlafondAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $addTotal = 0;
        $saldoTotal = 0;

        for ($i = 1; $i <= 12; $i++) {
            $newAdd = (float) ($data["V_BDGT_ADDMONTH{$i}"] ?? 0);
            $data["V_BDGT_ADDMONTH{$i}"] = $newAdd;

            $oldSaldo = (float) ($this->record->{"V_BDGT_SALDOMONTH{$i}"} ?? 0);
            $oldAdd = (float) ($this->record->{"V_BDGT_ADDMONTH{$i}"} ?? 0);
            $saldoAwal = $oldSaldo - $oldAdd;
            $saldo = $saldoAwal + $newAdd;
            $data["V_BDGT_SALDOMONTH{$i}"] = $saldo;

            $addTotal += $newAdd;
            $saldoTotal += $saldo;
        }

        $data['V_BDGT_ADDTOTAL'] = $addTotal;
        $data['V_BDGT_SALDOTOTAL'] = $saldoTotal;
        $data['V_BDGT_PLANTOTAL'] = $addTotal;

        $data['V_BDGT_PLANTOTAL'] = $addTotal;

        return $data;
    }
}
