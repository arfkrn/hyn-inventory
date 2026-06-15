<?php

namespace App\Filament\Resources\BahanKeluars\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BahanKeluarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengeluaran Bahan')
                    ->components([
                        TextEntry::make('tanggal')
                            ->color('gray'),
                        TextEntry::make('keterangan')
                            ->color('gray'),
                        TextEntry::make('user.name')->label('Diinput oleh')
                            ->color('gray')
                    ]),

                Section::make('Informasi Detail Bahan')
                    ->components([
                        RepeatableEntry::make('Daftar bahan')
                            ->state(fn ($record) => $record->items->map(fn ($item) => [
                                'jumlah' => $item->jumlah,
                                'bahan' => [
                                    'nama_bahan' => $item->bahan?->nama_bahan,
                                ],
                            ])->toArray())
                            ->schema([
                                TextEntry::make('bahan.nama_bahan')
                                    ->color('gray'),
                                TextEntry::make('jumlah')
                                    ->color('gray')
                            ])
                    ])
            ]);
    }
}
