<?php

namespace App\Filament\Resources\BahanMasuks\Pages;

use App\Filament\Resources\BahanMasuks\BahanMasukResource;
use App\Filament\Resources\BahanMasuks\Schemas\BahanMasukTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListBahanMasuks extends ListRecords
{
    protected static string $resource = BahanMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('buat transaksi')
                ->label('Tambah Bahan Masuk'),
        ];
    }

    public function table(Table $table): Table
    {
        return BahanMasukTable::configure($table);
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Bahan Masuk',
            'Daftar',
        ];
    }
}
