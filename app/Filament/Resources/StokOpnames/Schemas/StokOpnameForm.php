<?php

namespace App\Filament\Resources\StokOpnames\Schemas;

use App\Models\Bahan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StokOpnameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                ->columnSpanFull()
                ->schema([
                    Section::make('Detail stok opname')
                        ->schema([
                            DatePicker::make('tanggal')
                                ->default(now())
                                ->maxDate(now())
                                ->required(),
                            Textarea::make('keterangan')
                                ->required(),
                        ]),
                    
                    Section::make('Detail bahan')
                        ->schema([
                            Repeater::make('items')
                                ->relationship('items')
                                ->label('Daftar bahan')
                                ->disableItemDeletion(fn ($state) => count($state) <= 1)
                                ->addActionLabel('Tambah Item Bahan')
                                ->schema([
                                    Select::make('bahan_id')
                                        ->label('Nama bahan')
                                        ->options(Bahan::pluck('nama_bahan', 'id'))
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $stok = Bahan::find($state)?->stok ?? 0;
                                            $set('stok_sistem', $stok);
                                            $set('selisih', null);
                                        })
                                        ->required(),
                                    
                                    TextInput::make('stok_sistem')
                                        ->disabled()
                                        ->dehydrated(true),

                                    TextInput::make('stok_fisik')
                                        ->numeric()
                                        ->required()
                                        ->lazy()
                                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                            $stokSistem = $get('stok_sistem') ?? 0;
                                            $selisih = $state - $stokSistem;
                                            $set('selisih', $selisih);
                                        }),
                                    
                                    TextInput::make('selisih')
                                        ->prefixIcon(fn ($state) => (int)$state < 0 ?       'heroicon-m-minus-circle' : 'heroicon-m-plus-circle')
                                        ->prefixIconColor(fn ($state) => (int)$state < 0 ? 'danger' : 'success')
                                        ->disabled()
                                        ->dehydrated()
                                        ->reactive()
                                ])
                        ])
                ])
            ]);
    }
}
