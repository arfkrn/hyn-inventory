<?php

namespace App\Filament\Resources\StokOpnames;

use App\Filament\Resources\StokOpnames\Pages\CreateStokOpname;
use App\Filament\Resources\StokOpnames\Pages\EditStokOpname;
use App\Filament\Resources\StokOpnames\Pages\ListStokOpnames;
use App\Filament\Resources\StokOpnames\Pages\ViewStokOpname;
use App\Models\StokOpname;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class StokOpnameResource extends Resource
{
    protected static ?string $model = StokOpname::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;


    protected static ?string $navigationLabel = 'Stok Opname';

    protected static ?string $pluralModelLabel = 'Daftar Stok Opname';

    protected static ?string $breadcrumb = 'Stok Opname';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([       
                'items.bahan'   
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStokOpnames::route('/'),
            'create' => CreateStokOpname::route('/create'),
            'view' => ViewStokOpname::route('/{record}'),
            'edit' => EditStokOpname::route('/{record}/edit'),
        ];
    }
}
