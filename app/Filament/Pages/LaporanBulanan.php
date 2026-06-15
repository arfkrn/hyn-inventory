<?php

namespace App\Filament\Pages;

use App\Models\Bahan;
use App\Models\PurchaseOrderDetail;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use BackedEnum;
use Illuminate\Support\Facades\DB;

class LaporanBulanan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    protected static ?string $navigationLabel = 'Laporan Bulanan';
    protected static ?string $title = 'Cetak Laporan Terpadu';
    protected string $view = 'filament.pages.laporan-bulanan';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('direktur') || auth()->user()->hasRole('kepala gudang');
    }

    public function mount(): void
    {
        $this->form->fill([
            'bulan' => date('m'),
            'tahun' => date('Y'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Filter Laporan Terpadu')
                    ->description('Pilih periode untuk mengenerate laporan mutasi, analisis PO, dan opname.')
                    ->schema([
                        Select::make('bulan')
                            ->options([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])
                            ->native(false)
                            ->required(),
                        Select::make('tahun')
                            ->options(array_combine(range(2024, 2030), range(2024, 2030)))
                            ->native(false)
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('cetak')
                ->label('Cetak Laporan')
                ->submit('cetak')
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }

    public function cetak()
    {
        $input = $this->form->getState();
        $bulan = $input['bulan'];
        $tahun = $input['tahun'];

        // Ambil data bahan dengan relasi yang diperlukan untuk 3 seksi laporan
        $bahans = Bahan::with([
            'bahanMasukItems' => fn($q) => $q->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun),
            'bahanKeluarItems' => fn($q) => $q->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun),
            'stokOpnameItems' => fn($q) => $q->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->latest(),
        ])->get();

        // Ambil total Qty yang dipesan di PO pada bulan tersebut untuk Seksi II
        $poData = PurchaseOrderDetail::whereHas('purchaseOrder', function($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal_po', $bulan)->whereYear('tanggal_po', $tahun);
            })
            ->select('bahan_id', DB::raw('SUM(jumlah) as total_po'))
            ->groupBy('bahan_id')
            ->pluck('total_po', 'bahan_id');

        $reportData = $bahans->map(function ($bahan) use ($poData) {
            $opname = $bahan->stokOpnameItems->first();
            
            return [
                'nama' => $bahan->nama_bahan,
                'satuan' => $bahan->satuan,
                
                // Data Mutasi (Seksi I)
                'masuk' => $bahan->bahanMasukItems->sum('jumlah') ?? 0,
                'keluar' => $bahan->bahanKeluarItems->sum('jumlah') ?? 0,
                'tgl_masuk' => $bahan->bahanMasukItems->max('created_at')?->format('d/m/Y') ?? '-',
                'tgl_keluar' => $bahan->bahanKeluarItems->max('created_at')?->format('d/m/Y') ?? '-',
                
                // Data Analisis PO (Seksi II)
                'qty_po' => $poData[$bahan->id] ?? 0,
                
                // Data Opname (Seksi III) - PERBAIKAN DI SINI
                'stok_sistem' => $opname ? $opname->stok_sistem : $bahan->stok,
                'stok_fisik' => $opname ? $opname->stok_fisik : $bahan->stok,
                'selisih' => $opname ? $opname->selisih : 0, // Ambil langsung kolom selisih dari DB opname
                'tgl_opname' => $opname ? $opname->created_at->format('d/m/Y') : '-',
            ];
        });

        $pdf = Pdf::loadView('pdf.laporan', [
            'data' => $reportData,
            'bulan' => Carbon::create()->month((int) $bulan)->translatedFormat('F'),
            'tahun' => $tahun,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn() => print($pdf->output()), 
            "Laporan_Inventory_HMP_{$bulan}_{$tahun}.pdf"
        );
    }
}
