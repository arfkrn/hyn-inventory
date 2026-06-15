<?php

namespace App\Filament\Widgets;

use App\Models\Bahan;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class StokKritisTable extends TableWidget
{
    protected static ?string $heading = 'Daftar bahan dengan stok kritis';

    // 1. AKTIFKAN LAZY LOADING: Membuat tabel ini loading di latar belakang
    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // 2. Tambahkan limit atau pengurutan jika diperlukan agar query konstan ringan
                Bahan::query()
                    ->whereColumn('stok', '<=', 'min_stok')
                    ->oldest('stok') // Menampilkan stok yang paling tipis di atas
            )
            // 3. Batasi jumlah baris per halaman khusus untuk tampilan dashboard
            ->defaultPaginationPageOption(5) 
            ->columns([
                TextColumn::make('nama_bahan')
                    ->label('Nama Bahan'),

                TextColumn::make('stok')
                    ->label('Stok Saat Ini')
                    ->badge()
                    ->color('danger'), 

                TextColumn::make('min_stok')
                    ->label('Batas Minimum'),
            ]);
            // Bagian actions kosong bisa dibiarkan atau dihapus jika ingin kodenya lebih bersih
    }
}

// class StokKritisTable extends TableWidget
// {
//     protected static ?string $heading = 'Daftar bahan dengan stok kritis';

//     public function table(Table $table): Table
//     {
//         return $table
//             ->query(Bahan::query()->whereColumn('stok', '<=', 'min_stok'))
//             ->columns([
//                 TextColumn::make('nama_bahan')
//                     ->label('Nama Bahan'),

//                 TextColumn::make('stok')
//                     ->label('Stok Saat Ini')
//                     ->badge()
//                     ->color('danger'), // Memberi warna merah agar mencolok

//                 TextColumn::make('min_stok')
//                     ->label('Batas Minimum'),
//             ])
//             ->filters([
//                 //
//             ])
//             ->headerActions([
//                 //
//             ])
//             ->recordActions([
//                 //
//             ])
//             ->toolbarActions([
//                 BulkActionGroup::make([
//                     //
//                 ]),
//             ]);
//     }
// }
