<?php

namespace App\Filament\Resources\BahanKeluars;

use App\Filament\Resources\BahanKeluars\Pages\CreateBahanKeluar;
use App\Filament\Resources\BahanKeluars\Pages\EditBahanKeluar;
use App\Filament\Resources\BahanKeluars\Pages\ListBahanKeluars;
use App\Filament\Resources\BahanKeluars\Pages\ViewBahanKeluar;
use App\Models\BahanKeluar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class BahanKeluarResource extends Resource
{
    protected static ?string $model = BahanKeluar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpOnSquare;

    protected static ?string $navigationLabel = 'Bahan Keluar';

    protected static ?string $pluralModelLabel = 'Bahan Keluar';

    protected static ?string $breadcrumb = 'Bahan Keluar';

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->with([
            'user',
            'items.bahan',
        ]);
}

    public static function getPages(): array
    {
        return [
            'index' => ListBahanKeluars::route('/'),
            'create' => CreateBahanKeluar::route('/create'),
            'view' => ViewBahanKeluar::route('/{record}'),
            'edit' => EditBahanKeluar::route('/{record}/edit'),
        ];
    }
}
