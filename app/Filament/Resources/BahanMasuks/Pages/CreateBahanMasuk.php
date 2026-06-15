<?php

namespace App\Filament\Resources\BahanMasuks\Pages;

use App\Filament\Resources\BahanMasuks\BahanMasukResource;
use App\Filament\Resources\BahanMasuks\Schemas\BahanMasukForm;
use App\Services\BahanMasukService;
use App\Models\PurchaseOrder;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\Action as NotificationAction;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Bahan;
use App\Models\User;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class CreateBahanMasuk extends CreateRecord
{
    protected static string $resource = BahanMasukResource::class;

    protected static ?string $title = 'Buat Bahan Masuk Baru';

    public function form(Schema $schema): Schema
    {
        return BahanMasukForm::configure($schema);
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan')
            ->action(function () {
                DB::transaction(fn() => $this->create());
            })
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi simpan')
            ->modalDescription('Apakah data input sudah benar?, anda dapat mengubah data ini dalam 24 jam kedepan, setelah itu data tidak dapat diubah lagi.')
            ->modalSubmitActionLabel('Ya, simpan')
            ->modalCancelActionLabel('Batal');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction()
                ->label('Batal')
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // 1. Simpan Header (BahanMasuk)
            // Kita pisahkan data 'items' karena 'items' bukan kolom di tabel bahan_masuks
            $items = $data['items'] ?? [];
            unset($data['items']);

            $record = static::getModel()::create($data);

            // 2. Simpan Detail (BahanMasukItem) secara manual
            foreach ($items as $item) {
                // Kita gunakan relasi model untuk insert
                $record->items()->create([
                    'bahan_id' => $item['bahan_id'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'] ?? null,
                ]);
            }

            return $record;
        });
    }

    protected function afterCreate(): void
    {
        // Kita tidak perlu DB::transaction lagi di sini karena sudah ada di handleRecordCreation
        $bahanMasuk = $this->record;
        $userAdmin = User::role('admin')->get();

        // 1. LOGIKA PENAMBAHAN STOK
        // Sekarang items sudah pasti ada karena sudah diinsert di handleRecordCreation
        foreach ($bahanMasuk->items as $item) {
            $bahan = Bahan::find($item->bahan_id);
            if ($bahan) {
                $bahan->increment('stok', $item['jumlah']);
            }
        }

        // 2. Update status PO
        if ($bahanMasuk->purchase_order_id) {
            $po = PurchaseOrder::with('items')->find($bahanMasuk->purchase_order_id);

            if ($po) {
                $isFinished = true;
                foreach ($po->items as $poItem) {
                    // Hitung total akumulasi yang sudah diterima (termasuk yang barusan)
                    $totalDiterima = \App\Models\BahanMasukItem::whereHas('bahanMasuk', function ($query) use ($po) {
                        $query->where('purchase_order_id', $po->id);
                    })->where('bahan_id', $poItem->bahan_id)->sum('jumlah');

                    if ($totalDiterima < $poItem->jumlah) {
                        $isFinished = false;
                        break;
                    }
                }

                if ($isFinished) {
                    Notification::make()
                        ->title('Penerimaan PO Selesai Sepenuhnya')
                        ->success() // Warna hijau sukses
                        ->body("Semua item bahan untuk PO #{$po->no_po} telah diterima lengkap.")
                        ->actions([
                            NotificationAction::make('lihat') // Memakai class v4
                                ->label('Lihat detail')
                                ->button()
                                ->url(route('filament.admin.resources.purchase-orders.view', ['record' => $po->id]))
                                ->markAsRead()
                        ])
                        ->sendToDatabase($userAdmin);
                } else {
                    Notification::make()
                        ->title('Penerimaan PO Belum Lengkap')
                        ->warning() // Warna kuning peringatan
                        ->body("Gudang telah menerima bahan untuk PO #{$po->no_po}, namun status BELUM LENGKAP.")
                        ->actions([
                            NotificationAction::make('lihat') // Memakai class v4
                                ->label('Lihat detail')
                                ->button()
                                ->url(route('filament.admin.resources.purchase-orders.view', ['record' => $po->id]))
                                ->markAsRead()
                        ])
                        ->sendToDatabase($userAdmin);
                }

                $po->update([
                    'status' => $isFinished ? 'selesai' : 'belum_lengkap'
                ]);
            }
        }
    }


    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Tersimpan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Bahan Masuk',
            'Transaksi baru',
        ];
    }
}
