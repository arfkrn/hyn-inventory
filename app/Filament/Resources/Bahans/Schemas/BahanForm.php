<?php

namespace App\Filament\Resources\Bahans\Schemas;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BahanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Masukan detail bahan')
                            ->schema([
                                TextInput::make('nama_bahan')
                                    ->required()
                                    ->unique(),
                                Select::make('satuan')
                                    ->options([
                                        'pcs' => 'pcs',
                                        'ball' => 'ball',
                                        'krat' => 'krat'
                                    ])
                                    ->required(),
                                TextInput::make('min_stok')
                                    ->label('Minimum stok')
                                    ->numeric()
                                    ->required()
                                    ->rules([
                                        fn (): Closure => function (string $attribute, $value, Closure $fail) {
                                            if ($value <= 0) {
                                                $fail('Minimum stok tidak boleh negatif');
                                            }
                                        }
                                    ]),
                            ])
                    ])
            ]);
    }
}
