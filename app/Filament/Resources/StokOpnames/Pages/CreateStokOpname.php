<?php

namespace App\Filament\Resources\StokOpnames\Pages;

use App\Filament\Resources\StokOpnames\Schemas\StokOpnameForm;
use App\Filament\Resources\StokOpnames\StokOpnameResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateStokOpname extends CreateRecord
{
    protected static string $resource = StokOpnameResource::class;

    protected static ?string $title = 'Buat Stok Opaname Baru';

    public function form(Schema $schema): Schema
    {
        return StokOpnameForm::configure($schema);
    }

    public function canCreateAnother(): bool {
        return false;
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
                ->modalDescription('Konfirmasi simpan stok opname, ini akan memperbarui stok bahan sesuai dengan stok fisik yang diinput, pastikan data sudah benar sebelum menyimpan.')
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
            static::getResource()::getUrl('index') => 'Stok Opname',
            'Tambah',
        ];
    }
}
