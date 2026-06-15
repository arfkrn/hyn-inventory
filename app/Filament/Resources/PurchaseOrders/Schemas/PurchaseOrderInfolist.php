<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use App\Models\Bahan;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Schemas\Schema;
use Filament\Actions\Action;

class PurchaseOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi PO')
                    ->headerActions([
                        Action::make('status')
                            ->label(fn($record) => $record->status)
                            ->badge()
                            ->color(fn($record): string => match ($record->status) {
                                'selesai' => 'success',
                                'belum_lengkap' => 'warning',
                                'proses' => 'gray',
                                'dibatalkan' => 'danger'
                            })
                            ->disabled(),
                    ])
                    ->components([
                        TextEntry::make('tanggal_po')
                            ->label('Tanggal')
                            ->color('gray'),
                        TextEntry::make('no_po')
                            ->label('No PO')
                            ->color('gray'),
                        TextEntry::make('nama_supplier')
                            ->label('Nama supplier')
                            ->color('gray'),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->color('gray'),
                        TextEntry::make('user.name')
                            ->label('Dibuat oleh')
                            ->color('gray'),
                    ])->columns(2),

                Section::make('Daftar Bahan')
                    ->components([
                        Grid::make(6)
                            ->schema([
                                TextEntry::make('h1')->hiddenLabel()->state('Nama Bahan')->weight('bold')->columnSpan(2),
                                TextEntry::make('h2')->hiddenLabel()->state('Jumlah')->weight('bold')->columnSpan(2),
                                TextEntry::make('h3')->hiddenLabel()->state('Satuan')->weight('bold')->columnSpan(2),
                            ]),
                        RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->columns(6)
                            ->schema([
                                TextEntry::make('bahan.nama_bahan')
                                    ->hiddenLabel()
                                    ->columnSpan(2),
                                TextEntry::make('jumlah')
                                    ->hiddenLabel()
                                    ->columnSpan(2),
                                TextEntry::make('satuan')
                                    ->hiddenLabel()
                                    ->columnSpan(2),
                            ]),
                    ]),

                Grid::make(1)
                    ->schema([
                        Action::make('batalkan')
                            ->label('Batalkan')
                            ->button()
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Batalkan Purchase Order')
                            ->modalDescription('Apakah Anda yakin ingin membatalkan Purchase Order ini?')
                            ->modalSubmitActionLabel('Ya, batalkan')
                            ->action(function ($record) {
                                $record->update(['status' => 'dibatalkan']);
                            })
                            ->visible(fn($record) => !in_array($record->status, ['dibatalkan', 'selesai']) && auth()->user()->hasRole('admin')),

                        Action::make('cetak')
                            ->label('Cetak PDF')
                            ->button()
                            ->color('info')
                            ->icon('heroicon-o-printer')
                            ->url(fn($record) => route('po.print', ['purchaseOrder' => $record->id]))
                            ->openUrlInNewTab(),
                    ])
            ]);
    }
}