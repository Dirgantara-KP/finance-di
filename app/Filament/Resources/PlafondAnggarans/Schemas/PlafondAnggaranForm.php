<?php

namespace App\Filament\Resources\PlafondAnggarans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlafondAnggaranForm
{
    private const MONTH_FIELDS = [
        'V_BDGT_ADDMONTH1' => 'Januari',
        'V_BDGT_ADDMONTH2' => 'Februari',
        'V_BDGT_ADDMONTH3' => 'Maret',
        'V_BDGT_ADDMONTH4' => 'April',
        'V_BDGT_ADDMONTH5' => 'Mei',
        'V_BDGT_ADDMONTH6' => 'Juni',
        'V_BDGT_ADDMONTH7' => 'Juli',
        'V_BDGT_ADDMONTH8' => 'Agustus',
        'V_BDGT_ADDMONTH9' => 'September',
        'V_BDGT_ADDMONTH10' => 'Oktober',
        'V_BDGT_ADDMONTH11' => 'November',
        'V_BDGT_ADDMONTH12' => 'Desember',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Penambahan Anggaran Bulanan')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Grid::make(3)
                            ->schema(
                                array_map(
                                    fn (string $field, string $label) => TextInput::make($field)
                                        ->label($label)
                                        ->numeric()
                                        ->step(0.01)
                                        ->default(0)
                                        ->required(),
                                    array_keys(self::MONTH_FIELDS),
                                    array_values(self::MONTH_FIELDS),
                                ),
                            ),
                    ]),
            ]);
    }
}
