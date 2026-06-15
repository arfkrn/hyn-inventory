<?php

namespace App\Filament\Widgets;

use App\Models\Bahan;
use App\Models\BahanKeluarItem;
use App\Models\BahanMasukItem;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BahanTransactionOverview extends StatsOverviewWidget
{
    // 1. AKTIFKAN LAZY LOADING: Dashboard akan terbuka instan, widget ini akan loading menyusul
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        // Ambil waktu awal hari ini (00:00:00)
        $todayStart = Carbon::today(); 

        // Ganti whereDate dengan perbandingan biasa (>=) agar database bisa menggunakan INDEX
        $bahanMasuk = BahanMasukItem::whereHas('bahanMasuk', function ($query) use ($todayStart) {
            $query->where('tanggal', '>=', $todayStart);
        })->sum('jumlah');

        $bahanKeluar = BahanKeluarItem::whereHas('bahanKeluar', function ($query) use ($todayStart) {
            $query->where('tanggal', '>=', $todayStart);
        })->sum('jumlah');

        $totalBahanKritis = Bahan::whereColumn('stok', '<=', 'min_stok')->count();

        return [
            Stat::make('Total bahan masuk hari ini', number_format($bahanMasuk)),
            Stat::make('Total bahan keluar hari ini', number_format($bahanKeluar)),
            Stat::make('Total bahan dengan stok kritis', number_format($totalBahanKritis))
                ->description(($totalBahanKritis > 0) ? 'Segera lakukan pemesanan' : '')
                ->color(($totalBahanKritis > 0) ? 'danger' : 'success'),
        ];  
    }
}

// class BahanTransactionOverview extends StatsOverviewWidget
// {
//     protected function getStats(): array
//     {
//         $bahanMasuk = BahanMasukItem::whereHas('bahanMasuk', function ($query) {
//             $query->whereDate('tanggal', Carbon::today());
//         })->sum('jumlah');

//         $bahanKeluar = BahanKeluarItem::whereHas('bahanKeluar', function ($query) {
//             $query->whereDate('tanggal', Carbon::today());
//         })->sum('jumlah');

//         $totalBahanKritis = Bahan::whereColumn('stok', '<=', 'min_stok')->count();

//         return [
//             Stat::make('Total bahan masuk hari ini', number_format($bahanMasuk)),

//             Stat::make('Total bahan keluar hari ini', number_format($bahanKeluar)),

//             Stat::make('Total bahan dengan stok kritis', number_format($totalBahanKritis))
//                 ->description(($totalBahanKritis > 0) ? 'Segera lakukan pemesanan' : '')
//                 ->color(($totalBahanKritis > 0) ? 'danger' : 'success'),
//         ];  
//     }
// }
