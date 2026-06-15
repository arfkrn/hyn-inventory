<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Resources\Pages\CreateRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Actions\Action as NotificationAction;
use App\Models\User;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected static ?string $title = 'Buat PO Baru';

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Simpan & Cetak'),
            $this->getCancelFormAction()->label('Batal'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $po = $this->record;
        $userGudang = User::role('kepala gudang')->get();
        $po->load(['items.bahan']);

        // 1. Generate PDF
        $pdf = Pdf::loadView('pdf.purchase-order', ['po' => $po]);

        // 2. Simpan ke Storage (disk public agar bisa diakses via web)
        $safeNoPo = str_replace(['/', '\\'], '-', $po->no_po);
        $fileName = "PO-{$safeNoPo}.pdf";
        $filePath = "purchase-orders/{$fileName}";

        // Pastikan direktori ada (opsional karena put otomatis buat folder)
        Storage::disk('public')->put($filePath, $pdf->output());

        // 3. Kirim Notifikasi dengan Tombol Download
        Notification::make()
            ->success()
            ->title('PO Berhasil Dibuat')
            ->body("No PO: {$po->no_po}")
            ->actions([
                NotificationAction::make('download')
                    ->label('Unduh PDF')
                    ->url(route('po.print', ['purchaseOrder' => $po->id]))
                    ->openUrlInNewTab()
                    ->button(),
            ])
            ->send();

        if ($userGudang->count() > 0) {
            Notification::make()
                ->title('Ada PO Baru!')
                ->icon('heroicon-o-document-text')
                ->iconColor('success')
                ->body("Admin telah membuat PO baru dengan nomor {$po->no_po}.")
                ->actions([
                    NotificationAction::make('lihat') // Memakai class v4
                        ->label('Lihat detail')
                        ->button()
                        ->url(route('filament.admin.resources.purchase-orders.view', ['record' => $po->id]))
                        ->markAsRead()
                ])
                ->sendToDatabase($userGudang);
        }
    }

    // Matikan notifikasi default agar tidak double
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
