<?php

namespace App\Filament\Resources\BahanMasuks\Schemas;

use App\Filament\Resources\BahanMasuks\BahanMasukResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BahanMasukTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('items')->latest('created_at'))     
            ->columns([
                TextColumn::make('tanggal')->date('d M Y'),
                TextColumn::make('nama_supplier')->label('Supplier'),
                TextColumn::make('items_count')->label('Jumlah bahan'),
            ])
            ->recordUrl(null)
            ->actions([
                Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => BahanMasukResource::getUrl('view', ['record' => $record])),

            ]);
    }
}