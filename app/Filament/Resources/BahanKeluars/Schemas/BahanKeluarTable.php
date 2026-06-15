<?php

namespace App\Filament\Resources\BahanKeluars\Schemas;

use App\Filament\Resources\BahanKeluars\BahanKeluarResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BahanKeluarTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('items')->latest('tanggal'))
            ->columns([
                TextColumn::make('tanggal')->date('d M Y'),
                TextColumn::make('keterangan'),
                TextColumn::make('items_count')->label('Jumlah bahan')
            ])
            ->recordUrl(null)
            ->actions([
                Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => BahanKeluarResource::getUrl('view', ['record' => $record])),
            ]);
    }
}