<?php

namespace App\Filament\Resources\PlafondAnggarans;

use App\Filament\Resources\PlafondAnggarans\Pages\CreatePlafondAnggaran;
use App\Filament\Resources\PlafondAnggarans\Pages\EditPlafondAnggaran;
use App\Filament\Resources\PlafondAnggarans\Pages\ListPlafondAnggarans;
use App\Filament\Resources\PlafondAnggarans\Schemas\PlafondAnggaranForm;
use App\Filament\Resources\PlafondAnggarans\Tables\PlafondAnggaransTable;
use App\Models\TmbdgtPlafond;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlafondAnggaranResource extends Resource
{
    protected static ?string $model = TmbdgtPlafond::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'plafond_anggaran';

    protected static ?string $navigationLabel = 'Plafond Anggaran';

    public static function form(Schema $schema): Schema
    {
        return PlafondAnggaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlafondAnggaransTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlafondAnggarans::route('/'),
            'create' => CreatePlafondAnggaran::route('/create'),
            'edit' => EditPlafondAnggaran::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes(
            [SoftDeletingScope::class],
        );
    }
}
