<?php

namespace App\Filament\Resources\BahanKeluars\Pages;

use App\Filament\Resources\BahanKeluars\BahanKeluarResource;
use App\Filament\Resources\BahanKeluars\Schemas\BahanKeluarTable;
use App\Filament\Resources\BahanMasuks\Schemas\BahanMasukTable;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Table;

class ListBahanKeluars extends ListRecords
{
    protected static string $resource = BahanKeluarResource::class;
    // protected ?string $heading = 'Semua Transaksi'

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('buat transaksi')
                ->label('Tambah Bahan Keluar'),
        ];
    }

    public function table(Table $table): Table
    {
        return BahanKeluarTable::configure($table);
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Bahan Keluar',
            'Daftar', 
        ];
    }
}
