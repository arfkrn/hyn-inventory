<?php

namespace App\Filament\Resources\StokOpnames\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StokOpnameInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Stok Opname Bahan')
                    ->components([
                        TextEntry::make('tanggal'),
                        TextEntry::make('keterangan')
                    ]),

                Section::make('Informasi Detail Bahan')
                    ->components([
                        RepeatableEntry::make('Daftar bahan')
                            ->state(fn ($record) => $record->items->map(fn ($item) => [
                                'stok_sistem' => $item->stok_sistem,
                                'stok_fisik' => $item->stok_fisik,
                                'selisih' => $item->selisih,
                                'bahan' => [
                                    'nama_bahan' => $item->bahan?->nama_bahan,
                                ],
                            ])->toArray())
                            ->schema([
                                TextEntry::make('bahan.nama_bahan'),
                                TextEntry::make('stok_sistem'),
                                TextEntry::make('stok_fisik'),
                                TextEntry::make('selisih'),
                            ])
                    ])
            ]);
    }
}
