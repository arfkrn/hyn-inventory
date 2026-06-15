<?php

namespace App\Filament\Resources\StokOpnames\Schemas;

use App\Filament\Resources\StokOpnames\StokOpnameResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StokOpnameTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(fn ($query) => $query->withSum('items', 'selisih')->latest('tanggal'))
        ->columns([
                TextColumn::make('tanggal')
                    ->date('d M Y'),

                TextColumn::make('keterangan')
                    ->limit(30),

                TextColumn::make('items_sum_selisih')->label('Total selisih')
            ])
            ->recordUrl(null)
            ->actions([
                Action::make('detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => StokOpnameResource::getUrl('view', ['record' => $record]))
            ]);
    }
}