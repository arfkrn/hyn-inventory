<?php

namespace App\Filament\Resources\Bahans\Schemas;

use App\Filament\Resources\Bahans\BahanResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BahanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_bahan'),
                TextColumn::make('satuan'),
                TextColumn::make('min_stok'),
                TextColumn::make('stok')
                    ->badge()
                    ->color(fn ($record) =>
                        $record->stok <= $record->min_stok ? 'danger' : 'success'
                    ),
            ])
            ->actions([
                Action::make('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => BahanResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn () => auth()->user()->hasRole('admin')),
            ])
            ->recordUrl(null);
    }
}