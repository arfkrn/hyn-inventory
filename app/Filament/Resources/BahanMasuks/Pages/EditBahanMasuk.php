<?php

namespace App\Filament\Resources\BahanMasuks\Pages;

use App\Filament\Resources\BahanMasuks\BahanMasukResource;
use App\Filament\Resources\BahanMasuks\Schemas\BahanMasukForm;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bahan;
use Filament\Schemas\Schema;
use Filament\Forms\Components;

class EditBahanMasuk extends EditRecord
{
    protected static string $resource = BahanMasukResource::class;

    protected static ?string $title = 'Edit Transaksi';
    protected array $oldItems = [];

    public function form(Schema $schema): Schema
    {
        return BahanMasukForm::configure($schema, 'edit');  
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getSaveFormAction(): Action {
        return parent::getSaveFormAction()
            ->label('Simpan perubahan');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Batal');
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (! $this->record->canBeEdited()) {
            abort(403);
        }
    }

    protected function beforeSave(): void
    {
        $this->oldItems = $this->record->items()
        ->get()
        ->map(fn ($item) => [
            'bahan_id' => $item->bahan_id,
            'jumlah'   => $item->jumlah,
        ])
        ->toArray();
    }

    protected function afterSave(): void
    {
        DB::transaction(function () {

            foreach ($this->oldItems as $item) {
                Bahan::lockForUpdate()
                    ->find($item['bahan_id'])
                    ->decrement('stok', $item['jumlah']);
            }

            // ambil ulang data items terbaru dari DB
            $this->record->load('items');

            // terapkan stok baru
            foreach ($this->record->items as $item) {
                Bahan::lockForUpdate()
                    ->find($item->bahan_id)
                    ->increment('stok', $item->jumlah);
            }

        });
    }

    protected function afterDelete(): void
    {
        DB::transaction(function () {
            // 1. Kurangi stok bahan yang tadinya sudah ditambah
            foreach ($this->record->items as $item) {
                Bahan::lockForUpdate()
                    ->find($item->bahan_id)
                    ->decrement('stok', $item->jumlah);
            }

            // 2. Set status PO kembali ke 'proses' jika ada PO terkait
            if ($this->record->purchase_order_id) {
                $po = \App\Models\PurchaseOrder::find($this->record->purchase_order_id);
                if ($po && $po->status === 'selesai') {
                    $po->update(['status' => 'proses']);
                }
            }
        });
    }
}
