<?php

namespace App\Filament\Resources\PurchaseOrders\Tables;

use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->withCount('items')->latest('created_at'))
            ->columns([
                TextColumn::make('no_po')->label('No PO'),
                TextColumn::make('tanggal_po')->date('d M Y')->label('Tanggal PO'),
                TextColumn::make('nama_supplier')->label('Supplier'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'selesai' => 'success',
                        'proses' => 'gray',
                        'dibatalkan' => 'danger',
                        'checked_valid' => 'info',
                        'checked_mismatch' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => ucfirst(str_replace('_', ' ', $state))),
            ])
            ->recordUrl(null)
            ->actions([
                Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => PurchaseOrderResource::getUrl('view', ['record' => $record]))
                // ->visible(fn ($record) => !in_array($record->status, ['dibatalkan', 'selesai']) && auth()->user()->hasRole('admin')),

            ]);
    }
}
