<?php

namespace App\Filament\Widgets;

use App\Models\BahanKeluarItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\StokOpnameItem;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class StokChart extends ChartWidget
{
    protected ?string $heading = 'Grafik transaksi bahan 7 hari terakhir';

    protected function getData(): array
    {
        $purchaseOrderItems = Trend::model(PurchaseOrderDetail::class)
            ->between(start: now()->subDays(6), end: now())
            ->perDay()
            ->sum('jumlah_datang');

        $bahanKeluar = Trend::model(BahanKeluarItem::class)
            ->between(start: now()->subDays(6), end: now())
            ->perDay()
            ->sum('jumlah');

        return [
            'datasets' => [
            [
                'label' => 'Jumlah Datang PO',
                'data' => $purchaseOrderItems->map(fn (TrendValue $value) => $value->aggregate), 
                'borderColor' => '#10b981',
                'tension' => 0.1
            ],
            [
                'label' => 'Bahan Keluar',
                'data' => $bahanKeluar->map(fn (TrendValue $value) => $value->aggregate), 
                'borderColor' => '#ef4444',
                'tension' => 0.1
            ],
        ],
        'labels' => $purchaseOrderItems->map(fn (TrendValue $value) => $value->date)
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array {
        return [
            'scales' => [
                'y' => [
                    'min' => 0, 
                    'ticks' => [
                        'stepSize' => 1, 
                    ],
                ],
            ],
            'x' => [
                'ticks' => [
                    'maxRotation' => 0,
                    'font' => [
                        'size' => 10, 
                    ],
                ],
            ],
        ];
    }
}
