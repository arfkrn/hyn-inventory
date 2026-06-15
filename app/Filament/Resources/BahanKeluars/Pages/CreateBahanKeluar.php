<?php

namespace App\Filament\Resources\BahanKeluars\Pages;

use App\Filament\Resources\BahanKeluars\BahanKeluarResource;
use App\Filament\Resources\BahanKeluars\Schemas\BahanKeluarForm;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Bahan;
use Illuminate\Validation\ValidationException;


class CreateBahanKeluar extends CreateRecord
{
    protected static string $resource = BahanKeluarResource::class;

    protected static ?string $title = 'Buat Bahan Keluar Baru';

    public function form(Schema $schema): Schema
    {
        return BahanKeluarForm::configure($schema);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('Simpan')
                ->action(function (){
                    DB::transaction(fn () => $this->create());
                })
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi simpan')
                ->modalDescription('Apakah data input sudah benar?.')
                ->modalSubmitActionLabel('Ya, simpan')
                ->modalCancelActionLabel('Batal'),
            $this->getCancelFormAction()
                ->label('Batal')
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function () {

            // ambil item BARU
            $this->record->load('items');

            // group total per bahan
            $grouped = $this->record->items
                ->groupBy('bahan_id')
                ->map(fn ($items) => $items->sum('jumlah'));

            // VALIDASI STOK
            foreach ($grouped as $bahanId => $totalKeluar) {
                $bahan = Bahan::lockForUpdate()->find($bahanId);

                if ($bahan->stok < $totalKeluar) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$bahan->nama_bahan} tidak mencukupi.",
                    ]);
                }
            }

            // TERAPKAN STOK
            foreach ($grouped as $bahanId => $totalKeluar) {
                Bahan::find($bahanId)->decrement('stok', $totalKeluar);
            }

        });
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
            static::getResource()::getUrl('index') => 'Bahan Keluar',
            'Transaksi baru',
        ];
    }
}
