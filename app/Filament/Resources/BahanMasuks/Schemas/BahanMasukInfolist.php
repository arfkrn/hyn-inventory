<?php

namespace App\Filament\Resources\BahanMasuks\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class BahanMasukInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pemasukan Bahan')
                    ->components([
                        TextEntry::make('tanggal')->date('d M Y')
                            ->color('gray'),
                        TextEntry::make('nama_supplier')
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
                                'satuan' => $item->bahan?->satuan,
                                'bahan' => [
                                    'nama_bahan' => $item->bahan?->nama_bahan,
                                ],
                            ])->toArray())
                            ->schema([
                                TextEntry::make('bahan.nama_bahan')
                                    ->label('Nama Bahan')
                                    ->color('gray'),
                                TextEntry::make('jumlah')
                                    ->label('Jumlah Diterima')
                                    ->suffix(fn ($record) => ' ' . $record['satuan'])
                                    ->color('gray') 
                            ])
                            ->columns(2)
                    ])
            ]);
    } 
}