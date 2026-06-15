<?php

namespace App\Filament\Resources\BahanKeluars\Pages;

use App\Filament\Resources\BahanKeluars\BahanKeluarResource;
use App\Filament\Resources\BahanKeluars\Schemas\BahanKeluarForm;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Bahan;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class EditBahanKeluar extends EditRecord
{
    protected static string $resource = BahanKeluarResource::class;

    protected static ?string $title = 'Edit Transaksi';
    protected array $oldItems = [];

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

    public function form(Schema $schema): Schema
    {
        return BahanKeluarForm::configure($schema);
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
        $this->oldItems = $this->record
            ->items()
            ->get()
            ->map(fn ($item) => [
                'bahan_id' => $item->bahan_id,
                'jumlah' => $item->jumlah,
            ])
            ->toArray();
    }

    protected function afterSave(): void
    {
        DB::transaction(function () {

             // 1️⃣ rollback stok lama
            foreach ($this->oldItems as $item) {
                $itemBahan = Bahan::lockForUpdate()->find($item['bahan_id']);
                $itemBahan->increment('stok', $item['jumlah']);
            }

            // 2️⃣ ambil item BARU
            $this->record->load('items');

            // 3️⃣ VALIDASI TOTAL KEBUTUHAN PER BAHAN
            $grouped = $this->record->items
                ->groupBy('bahan_id')
                ->map(fn ($items) => $items->sum('jumlah'));

            foreach ($grouped as $bahanId => $totalKeluar) {
                $bahan = Bahan::lockForUpdate()->find($bahanId);

                if ($bahan->stok < $totalKeluar) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$bahan->nama_bahan} tidak mencukupi.",
                    ]);
                }
            }

            // 4️⃣ TERAPKAN STOK BARU
            foreach ($grouped as $bahanId => $totalKeluar) {
                Bahan::find($bahanId)->decrement('stok', $totalKeluar);
            }
        });
    }
}
