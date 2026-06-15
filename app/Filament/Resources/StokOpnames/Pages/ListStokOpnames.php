<?php

namespace App\Filament\Resources\StokOpnames\Pages;

use App\Filament\Resources\StokOpnames\Schemas\StokOpnameTable;
use App\Filament\Resources\StokOpnames\StokOpnameResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListStokOpnames extends ListRecords
{
    protected static string $resource = StokOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat stok opname baru'),
        ];
    }

    public function table(Table $table): Table
    {
        return StokOpnameTable::configure($table);
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Stok Opname',
            'Daftar',
        ];
    }
}
