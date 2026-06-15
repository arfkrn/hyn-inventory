<?php

namespace App\Filament\Resources\Bahans\Pages;

use App\Filament\Resources\Bahans\BahanResource;
use App\Filament\Resources\Bahans\Schemas\BahanForm;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateBahan extends CreateRecord
{
    protected static string $resource = BahanResource::class;

    protected static ?string $title = 'Tambah Bahan Baru';

    protected function getFormActions(): array
    {
        return [
            Action::make('Simpan')
                ->action(fn () => DB::transaction(fn () => $this->create()))
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi simpan')
                ->modalDescription('Apakah anda sudah yakin data ini sudah benar?')
                ->modalSubmitActionLabel('Ya, simpan')
                ->modalCancelActionLabel('Batal'),
            $this->getCancelFormAction()
                ->label('Batal')
        ];
    }

    public function form(Schema $schema): Schema
    {
        return BahanForm::configure($schema);
    }

    public function canCreateAnother(): bool {
        return false;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Tersimpan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Bahan',
            'Tambah bahan baru',
        ];
    }
}
