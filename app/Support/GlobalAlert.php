<?php

namespace App\Support;

use App\Models\Bahan;

class GlobalAlert 
{
    public static function lowStockCount(): string
    {
        return cache()->remember(
            'global_low_stock_count',
            now()->addMinutes(5),
            fn () => (string) Bahan::whereColumn('stok', '<=', 'min_stok')->count()
        );
    }
}