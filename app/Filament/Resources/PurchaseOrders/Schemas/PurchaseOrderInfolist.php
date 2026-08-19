<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use App\Models\Bahan;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\DB;

class PurchaseOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi PO')
                    ->headerActions([
                        Action::make('status')
                            ->label(fn($record) => match ($record->status) {
                                'checked_valid' => 'Divalidasi',
                                'checked_mismatch' => 'Belum Lengkap',
                                'proses' => 'Proses',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                                default => $record->status,
                            })
                            ->badge()
                            ->color(fn($record) => match ($record->status) {
                                'selesai' => 'success',
                                'proses' => 'gray',
                                'dibatalkan' => 'danger',
                                'checked_valid' => 'info',
                                'checked_mismatch' => 'warning',
                                default => 'gray',
                            })
                            ->disabled(),
                    ])
                    ->components([
                        TextEntry::make('tanggal_po')
                            ->label('Tanggal')
                            ->color('gray'),
                        TextEntry::make('no_po')
                            ->label('No PO')
                            ->color('gray'),
                        TextEntry::make('nama_supplier')
                            ->label('Nama supplier')
                            ->color('gray'),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->color('gray')
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label('Dibuat oleh')
                            ->color('gray'),
                    ])->columns(2),

                Section::make('Daftar Bahan')
                    ->schema([
                        Grid::make(7)
                            ->schema([
                                TextEntry::make('h1')->hiddenLabel()->state('Nama Bahan')->weight('bold')->columnSpan(2),
                                TextEntry::make('h2')->hiddenLabel()->state('Jumlah PO')->weight('bold')->columnSpan(2),
                                TextEntry::make('h3')->hiddenLabel()->state('Satuan')->weight('bold')->columnSpan(1),
                                TextEntry::make('h4')->hiddenLabel()->state('Fisik Datang')->weight('bold')->columnSpan(2),
                            ]),

                        RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->columns(7)
                            ->schema([
                                TextEntry::make('bahan.nama_bahan')->hiddenLabel()->columnSpan(2),
                                TextEntry::make('jumlah')->hiddenLabel()->columnSpan(2),
                                TextEntry::make('satuan')->hiddenLabel()->columnSpan(1),

                                TextEntry::make('jumlah_datang')
                                    ->hiddenLabel()
                                    ->columnSpan(2)
                                    ->weight('bold')
                                    // Mengubah teks jika data di database masih kosong/null
                                    ->state(function ($record) {
                                        $statusPo = $record->purchaseOrder?->status ?? 'proses';

                                        // Jika PO masih proses dan belum diisi gudang
                                        if ($statusPo === 'proses' && is_null($record->jumlah_datang)) {
                                            return 'Menunggu';
                                        }

                                        if (is_null($record->jumlah_datang)) {
                                            return $record->jumlah;
                                        }

                                        // Jika sudah diisi, tampilkan angkanya
                                        return $record->jumlah_datang;
                                    })
                                    // Mengubah warna teks secara dinamis
                                    ->color(function ($record) {
                                        if ($record->purchaseOrder->status === 'proses') {
                                            return 'warning'; // Warna kuning/oranye saat menunggu cek
                                        }

                                        // Jika jumlah fisik kurang dari jumlah PO setelah dicek
                                        return $record->jumlah_datang < $record->jumlah ? 'danger' : 'success';
                                    })
                                    ->helperText(fn($record) => $record->catatan_gudang),

                                // TextEntry::make('catatan_gudang')
                                //     ->columnSpan(2)
                                //     ->color('danger')
                                //     ->visible(fn($record) => !empty($record->catatan_gudang)),
                            ]),
                    ]),

                Grid::make(1)
                    ->schema([
                        Action::make('gudang_sesuai_semua')
                            ->label('Semua Sesuai')
                            ->button()
                            ->color('success')
                            ->icon('heroicon-o-check')
                            ->requiresConfirmation()
                            ->modalHeading('Konfirmasi Barang Sesuai')
                            ->modalDescription('Apakah Anda yakin semua fisik barang datang sudah sesuai tanpa ada kekurangan?')
                            ->visible(fn($record) => $record->status === 'proses' && auth()->user()->hasRole('kepala gudang'))
                            ->action(function ($record) {
                                foreach ($record->items as $item) {
                                    $item->update(['jumlah_datang' => $item->jumlah]);
                                }
                                $record->update(['status' => 'checked_valid']);
                            }),

                        // --- TOMBOL KEPALA GUDANG 2: TIDAK SESUAI (MUNCUL MODAL FORM) ---
                        Action::make('gudang_tidak_sesuai')
                            ->label('Tidak Sesuai')
                            ->button()
                            ->color('danger')
                            ->icon('heroicon-o-x-mark')
                            ->visible(fn($record) => $record->status === 'proses' && auth()->user()->hasRole('kepala gudang'))
                            ->modalHeading('Laporkan Kekurangan Barang')
                            ->modalWidth('4xl')
                            // Mengisi modal form dengan data item PO saat ini agar tinggal diedit bagian yang kurang
                            ->mountUsing(function ($form, $record) {
                                $form->fill([
                                    'items' => $record->items->map(fn($item) => [
                                        'id' => $item->id,
                                        'nama_bahan' => $item->bahan->nama_bahan,
                                        'jumlah' => $item->jumlah,
                                        'jumlah_datang' => $item->jumlah, // Default diisi sama, biar gudang cuma ubah yang kurang
                                        'catatan_gudang' => '',
                                    ])->toArray()
                                ]);
                            })
                            ->form([
                                Repeater::make('items')
                                    ->hiddenLabel()
                                    ->addable(false) // Mencegah gudang nambah item baru di luar PO
                                    ->deletable(false) // Mencegah gudang hapus baris barang
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextInput::make('nama_bahan')->label('Nama Bahan')->disabled(),
                                                TextInput::make('jumlah')->label('Jumlah PO')->disabled()->numeric(),
                                                TextInput::make('jumlah_datang')->label('Fisik Datang')->numeric()->required(),
                                                Textarea::make('catatan_gudang')->label('Catatan Kekurangan')->rows(1),
                                            ])
                                    ])
                            ])
                            ->action(function ($record, array $data) {
                                $adaKekurangan = false;

                                foreach ($data['items'] as $itemData) {
                                    $item = $record->items()->find($itemData['id']);
                                    if ($item) {
                                        $item->update([
                                            'jumlah_datang' => $itemData['jumlah_datang'],
                                            'catatan_gudang' => $itemData['catatan_gudang'],
                                        ]);

                                        if ((int) $itemData['jumlah_datang'] < $item->jumlah) {
                                            $adaKekurangan = true;
                                        }
                                    }
                                }

                                // Tentukan status PO induk berdasarkan inputan modal
                                $status = $adaKekurangan ? 'checked_mismatch' : 'checked_valid';
                                $record->update(['status' => $status]);
                            }),

                        // --- TOMBOL ADMIN 1: SETUJUI & TAMBAH STOK ---
                        Action::make('admin_approve_stok')
                            ->label('Setujui & Tambah Stok')
                            ->button()
                            ->color('success')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->requiresConfirmation()
                            ->visible(fn($record) => $record->status === 'checked_valid' && auth()->user()->hasRole('admin'))
                            ->action(function ($record) {
                                DB::transaction(function () use ($record) {
                                    foreach ($record->items as $item) {
                                        $item->bahan->increment('stok', $item->jumlah_datang ?? $item->jumlah);
                                    }
                                    $record->update(['status' => 'selesai']);
                                });
                            }),

                        // --- TOMBOL ADMIN 2: SPLIT & BACKORDER ---
                        Action::make('admin_split_backorder')
                            ->label('Split & Backorder')
                            ->button()
                            ->color('warning')
                            ->icon('heroicon-o-document-duplicate')
                            ->requiresConfirmation()
                            ->modalHeading('Proses Parsial & Backorder')
                            ->modalDescription('Sistem akan memasukkan stok fisik yang datang, dan membuat PO baru otomatis untuk sisa kekurangannya. Lanjutkan?')
                            ->visible(fn($record) => $record->status === 'checked_mismatch' && auth()->user()->hasRole('admin'))
                            ->action(function ($record) {
                                // Logika Cara ke-2 (Split & Backorder) ditaruh di sini
                                DB::transaction(function () use ($record) {
                                    // 1. Masukkan stok yang ada dulu
                                    foreach ($record->items as $item) {
                                        $item->bahan->increment('stok', $item->jumlah_datang);
                                    }
                                    // 2. Ubah status PO lama jadi selesai
                                    $record->update(['status' => 'selesai']);

                                    // duplikasi PO baru (Backorder)
                                    // duplikat po lama ditambah suffix 'b'
                                    $newPo = $record->no_po . 'b';

                                    // simpan header PO
                                    $poBackOrder = PurchaseOrder::create([
                                        'user_id' => auth()->user()->id,
                                        'no_po' => $newPo,
                                        'tanggal_po' => $record->tanggal_po,
                                        'nama_supplier' => $record->nama_supplier,
                                        'keterangan' => null,
                                    ]);

                                    // mendapatkan daftar bahan yang kurang
                                    $bahanKurang = $record->items->filter(
                                        fn($item) => $item->jumlah_datang < $item->jumlah
                                    );

                                    // simpan detail po
                                    foreach ($bahanKurang as $item) {
                                        PurchaseOrderDetail::create([
                                            'purchase_order_id' => $poBackOrder->id,
                                            'bahan_id' => $item->bahan_id,
                                            'jumlah' => $item->jumlah - $item->jumlah_datang,
                                            'satuan' => $item->satuan
                                        ]);
                                    }
                                });
                            }),

                        Action::make('batalkan')
                            ->label('Batalkan PO')
                            ->button()
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->update(['status' => 'dibatalkan']);
                            })
                            ->visible(fn($record) => !in_array($record->status, ['dibatalkan', 'selesai']) && auth()->user()->hasRole('admin')),

                        // --- TOMBOL UMUM: CETAK ---
                        Action::make('cetak')
                            ->label('Cetak PDF')
                            ->button()
                            ->color('info')
                            ->icon('heroicon-o-printer')
                            ->url(fn($record) => route('po.print', ['purchaseOrder' => $record->id]))
                            ->openUrlInNewTab(),
                    ])
            ]);
    }
}