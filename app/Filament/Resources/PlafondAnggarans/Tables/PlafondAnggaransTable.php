<?php

namespace App\Filament\Resources\PlafondAnggarans\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlafondAnggaransTable
{
    public static function configure(Table $table): Table
    {
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $monthColumns = array_map(
            fn (int $i, string $label) => TextColumn::make("month_{$i}")
                ->label($label)
                ->numeric(decimalPlaces: 2)
                ->sortable(false),
            range(0, 11),
            $monthLabels,
        );

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('No')
                    ->sortable(false),
                TextColumn::make('uraian')
                    ->label('Uraian')
                    ->sortable(false),
                ...$monthColumns,
                TextColumn::make('total')
                    ->label('Total')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(false),
            ])
            ->recordActions([])
            ->paginated(false)
            ->defaultSort(null);
    }
}
