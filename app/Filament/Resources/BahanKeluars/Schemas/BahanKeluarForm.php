<?php

namespace App\Filament\Resources\BahanKeluars\Schemas;

use App\Models\Bahan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BahanKeluarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Informasi Transaksi Bahan')
                            ->schema([
                                DatePicker::make('tanggal')
                                    ->default(now())
                                    ->required()
                                    ->maxDate(now()),
                                Textarea::make('keterangan')
                                    ->required()

                            ]),

                        Section::make('Informasi Detail Bahan')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship()
                                    ->label('Daftar bahan')
                                    ->addActionLabel('Tambah item bahan')
                                    ->minItems(1)
                                    ->disableItemDeletion(fn ($state) => count($state) <= 1)
                                    ->schema([
                                        Select::make('bahan_id')
                                            ->label('Nama bahan')
                                            ->relationship('bahan', 'nama_bahan')
                                            ->reactive()
                                            ->required(),
                                        TextInput::make('jumlah')
                                            ->label('Jumlah pengeluaran')
                                            ->numeric()
                                            ->minValue(1)
                                            ->reactive()
                                            ->hint(function ($record, $get) {
                                                $bahanId = $get('bahan_id');

                                                if ($record) {
                                                    return null;
                                                }

                                                $bahan = Bahan::find($bahanId);

                                                return 'Stok saat ini: ' . $bahan?->stok ?? 0;
                                            })
                                            ->required()
                                    ])
                            ])
                    ])
            ]);
    }
}
