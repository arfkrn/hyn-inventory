<?php

namespace App\Filament\Resources\Bahans;

use App\Filament\Resources\Bahans\Pages\CreateBahan;
use App\Filament\Resources\Bahans\Pages\EditBahan;
use App\Filament\Resources\Bahans\Pages\ListBahans;
use App\Models\Bahan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Support\GlobalAlert;

class BahanResource extends Resource
{
    protected static ?string $model = Bahan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Bahan';

    protected static ?string $pluralModelLabel = 'Semua Bahan';

    protected static ?string $breadcrumb = 'Bahan';

    public static function getNavigationBadge(): ?string
    {
        $count = GlobalAlert::lowStockCount();
        
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBahans::route('/'),
            'create' => CreateBahan::route('/create'),
            'edit' => EditBahan::route('/{record}/edit'),
        ];
    }
}
