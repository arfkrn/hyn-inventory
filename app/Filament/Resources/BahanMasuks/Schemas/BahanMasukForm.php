<?php

namespace App\Filament\Resources\BahanMasuks\Schemas;

use App\Models\BahanMasukItem;
use App\Models\PurchaseOrder;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Request;

class BahanMasukForm
{
    public static function configure(Schema $schema, string $mode = 'create'): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Informasi Transaksi Bahan')
                            ->schema([
                                Select::make('purchase_order_id')
                                    ->label('No PO (Proses)')
                                    ->options(PurchaseOrder::whereIn('status', ['proses', 'belum_lengkap'])->pluck('no_po', 'id'))
                                    ->searchable()
                                    ->default(fn () => Request::query('po_id'))
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $poId = $get('purchase_order_id');
                                        if ($poId) {
                                            $po = PurchaseOrder::with('items.bahan')->find($poId);
                                            if ($po) {
                                                $set('nama_supplier', $po->nama_supplier);
                                                $items = [];
                                                foreach ($po->items as $item) {
                                                    $jumlahDiterimaSebelumnya = DB::table('bahan_masuk_items')
                                                            ->join('bahan_masuk', 'bahan_masuk_items.bahan_masuk_id', '=', 'bahan_masuk.id')
                                                            ->where('bahan_masuk.purchase_order_id', $poId)
                                                            ->where('bahan_masuk_items.bahan_id', $item->bahan_id)
                                                            ->sum('bahan_masuk_items.jumlah');

                                                    $sisaPesanan = (int)$item->jumlah - (int)$jumlahDiterimaSebelumnya;

                                                    if ($sisaPesanan > 0) {
                                                        $items[] = [
                                                            'bahan_id' => $item->bahan_id,
                                                            'nama_bahan_display' => $item->bahan?->nama_bahan ?? '-',
                                                            'jumlah_dipesan' => $item->jumlah,
                                                            'sisa_pesanan' => $sisaPesanan,
                                                            'jumlah' => $sisaPesanan,
                                                            'satuan' => $item->satuan,
                                                        ];
                                                    }
                                                }
                                                $set('items', array_values($items));
                                            }
                                        } else {
                                            $set('items', []);
                                            $set('nama_supplier', null);
                                        }
                                    }),
                                DatePicker::make('tanggal')
                                    ->label('Tanggal Terima')
                                    ->default(now())
                                    ->required(),
                                TextInput::make('nama_supplier')
                                    ->readOnly()
                                    ->extraAttributes(['class' => 'bg-gray-50']),
                                Textarea::make('keterangan')
                                    ->label('Keterangan Penerimaan')
                                    ->required()
                            ]),

                        Section::make('Informasi Detail Bahan')
                            ->schema([
                                Repeater::make('items')
                                    ->label('Daftar bahan')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->live()
                                    ->columns(4)
                                    ->schema([
                                        Hidden::make('bahan_id'),
                                        TextInput::make('nama_bahan_display')
                                            ->label('Nama bahan')
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('jumlah_dipesan')
                                            ->label('Dipesan')
                                            ->disabled()
                                            ->dehydrated(false),
                                        TextInput::make('sisa_pesanan')
                                            ->label('Sisa Pesanan')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->extraInputAttributes(['class' => 'font-bold text-blue-600']),
                                        TextInput::make('jumlah')
                                            ->label('Diterima')
                                            ->numeric()
                                            ->required()
                                            ->live(onBlur: true)
                                            ->rules([
                                                fn (Get $get): Closure => function(string $attribute, $value, Closure $fail) use ($get) {
                                                    $sisa = (int) $get('sisa_pesanan');
                                                    if ($value <= 0) $fail('Jumlah minimal 1');
                                                    if ($value > $sisa) $fail("Maksimal sisa adalah {$sisa}");
                                                }
                                            ])
                                    ])
                            ])
                            ->disabled($mode === 'edit')
                    ])
            ]);
    }
}