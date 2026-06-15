<?php

namespace App\Filament\Resources\Bahans\Pages;

use App\Filament\Resources\Bahans\BahanResource;
use App\Filament\Resources\Bahans\Schemas\BahanForm;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditBahan extends EditRecord
{
    protected static string $resource = BahanResource::class;

    protected static ?string $title = 'Edit Bahan';

    protected function getSaveFormAction(): Action {
        return parent::getSaveFormAction()
            ->label('Simpan perubahan');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Batal');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi hapus')
                ->modalDescription('Apakah anda yakin ingin menghapus data ini?.')
                ->modalSubmitActionLabel('Ya, hapus')
                ->modalCancelActionLabel('Batal'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    public function form(Schema $schema): Schema
    {
        return BahanForm::configure($schema);
    }
}
