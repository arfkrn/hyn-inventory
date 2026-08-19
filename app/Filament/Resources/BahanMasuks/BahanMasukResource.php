<?php

namespace App\Filament\Resources\BahanMasuks;

use App\Filament\Resources\BahanMasuks\Pages\CreateBahanMasuk;
use App\Filament\Resources\BahanMasuks\Pages\ViewBahanMasuk;
use App\Filament\Resources\BahanMasuks\Pages\EditBahanMasuk;
use App\Filament\Resources\BahanMasuks\Pages\ListBahanMasuks;
use App\Models\BahanMasuk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class BahanMasukResource extends Resource
{
    protected static ?string $model = BahanMasuk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownOnSquare;

    protected static ?string $navigationLabel = 'Bahan Masuk';

    protected static ?string $pluralModelLabel = 'Daftar Bahan Masuk';

    protected static ?string $breadcrumb = 'Bahan Masuk';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'user',         // Menghilangkan N+1 pada TextEntry::make('user.name')
                'items.bahan'   // Menghilangkan N+1 pada perulangan items dan data bahan di dalamnya
            ]);
    }

    public static function canAccess(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBahanMasuks::route('/'),
            'create' => CreateBahanMasuk::route('/create'),
            'view' => ViewBahanMasuk::route('/{record}'),
            'edit' => EditBahanMasuk::route('/{record}/edit'),
        ];
    }
}
