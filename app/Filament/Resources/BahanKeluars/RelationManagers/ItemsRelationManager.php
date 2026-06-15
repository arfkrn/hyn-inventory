<?php

namespace App\Filament\Resources\BahanKeluars\RelationManagers;

use App\Filament\Resources\BahanKeluars\BahanKeluarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $relatedResource = BahanKeluarResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bahan.nama_bahan')
                    ->label('Nama Bahan'),
                Tables\Columns\TextColumn::make('jumlah'),
            ]);
    }
}
