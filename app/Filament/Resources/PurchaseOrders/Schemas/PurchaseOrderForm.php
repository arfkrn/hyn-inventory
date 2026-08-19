<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use App\Models\Bahan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Informasi PO')
                            ->schema([
                                DatePicker::make('tanggal_po')
                                    ->label('Tanggal')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('no_po')
                                    ->label('No PO')
                                    ->required()
                                    ->readOnly()
                                    ->unique('purchase_order', 'no_po', ignoreRecord: true)
                                    ->default(function () {
                                        $year = date('Y');
                                        $month = date('m');
                                        $prefix = "PO/{$year}/{$month}/";
                                        
                                        $lastPo = \App\Models\PurchaseOrder::where('no_po', 'like', $prefix . '%')
                                            ->orderBy('id', 'desc')
                                            ->first();
                                            
                                        if ($lastPo) {
                                            $lastNumber = (int) substr($lastPo->no_po, -3);
                                            $nextNumber = $lastNumber + 1;
                                        } else {
                                            $nextNumber = 1;
                                        }
                                        
                                        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                                    }),
                                TextInput::make('nama_supplier')
                                    ->label('Nama supplier')
                                    ->required(),
                                Textarea::make('keterangan')
                                    ->label('Keterangan')
                            ])->columnSpan(1),

                        Section::make('Informasi detail bahan')
                            ->schema([
                                Select::make('temp_bahan_id')
                                    ->label('Nama bahan')
                                    ->options(Bahan::pluck('nama_bahan', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $bahan = Bahan::find($state);
                                        if ($bahan) {
                                            $set('temp_satuan', $bahan->satuan);
                                        } else {
                                            $set('temp_satuan', null);
                                        }
                                    }),
                                TextInput::make('temp_jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->live()
                                    ->dehydrated(false),
                                TextInput::make('temp_satuan')
                                    ->label('Satuan')
                                    ->readOnly()
                                    ->dehydrated(false),
                                
                                Actions::make([
                                    Action::make('tambah_ke_daftar')
                                        ->label('Tambah ke daftar')
                                        ->action(function (Get $get, Set $set) {
                                            $bahanId = $get('temp_bahan_id');
                                            $jumlah = $get('temp_jumlah');
                                            $satuan = $get('temp_satuan');
                                            
                                            if (!$bahanId || !$jumlah) {
                                                return;
                                            }
                                            
                                            
                                            $items = $get('items') ?? [];
                                            
                                            $items[(string) Str::uuid()] = [
                                                'bahan_id' => $bahanId,
                                                'jumlah' => $jumlah,
                                                'satuan' => $satuan,
                                            ];
                                            
                                            $set('items', $items);
                                            
                                            // Reset temporary fields
                                            $set('temp_bahan_id', null);
                                            $set('temp_jumlah', null);
                                            $set('temp_satuan', null);
                                            
                                        })
                                ])->fullWidth(),
                            ]),

                        Section::make('Daftar Bahan')
                            ->columnSpanFull()
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('h1')->hiddenLabel()->content(new \Illuminate\Support\HtmlString('<div class="font-bold">Nama Bahan</div>'))->columnSpan(2),
                                        \Filament\Forms\Components\Placeholder::make('h2')->hiddenLabel()->content(new \Illuminate\Support\HtmlString('<div class="font-bold">Jumlah</div>'))->columnSpan(1),
                                        \Filament\Forms\Components\Placeholder::make('h3')->hiddenLabel()->content(new \Illuminate\Support\HtmlString('<div class="font-bold">Satuan</div>'))->columnSpan(1),
                                    ]),
                                Repeater::make('items')
                                    ->relationship()
                                    ->hiddenLabel()
                                    ->addable(false)
                                    ->reorderable(false)
                                    ->columns(4)
                                    ->default([])
                                    ->live()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('nama_bahan_text')
                                            ->hiddenLabel()
                                            ->content(fn(Get $get) => Bahan::find($get('bahan_id'))?->nama_bahan)
                                            ->columnSpan(2),
                                        \Filament\Forms\Components\Placeholder::make('jumlah_text')
                                            ->hiddenLabel()
                                            ->content(fn(Get $get) => $get('jumlah'))
                                            ->columnSpan(1),
                                        \Filament\Forms\Components\Placeholder::make('satuan_text')
                                            ->hiddenLabel()
                                            ->content(fn(Get $get) => $get('satuan'))
                                            ->columnSpan(1),

                                        \Filament\Forms\Components\Hidden::make('bahan_id'),
                                        \Filament\Forms\Components\Hidden::make('jumlah'),
                                        \Filament\Forms\Components\Hidden::make('satuan'),
                                    ]),
                            ]),
                    ])
            ]);
    }
}
