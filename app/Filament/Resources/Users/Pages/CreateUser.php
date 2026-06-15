<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use App\Services\UserService;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{

    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Tambah Pengguna Baru';

    protected UserService $userService;

    public function boot(): void {
        $this->userService = app(UserService::class);
    }

    public function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public function canCreateAnother(): bool {
        return false;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('Buat')
                ->action(function (){
                    $this->create();
                })
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi simpan')
                ->modalDescription('Konfirmasi pembuatan akun pengguna baru.')
                ->modalSubmitActionLabel('Ya, konfirmasi')
                ->modalCancelActionLabel('Batal'),
            $this->getCancelFormAction()
                ->label('Batal')
        ];
    }

    protected function handleRecordCreation(array $data): Model 
    {
        return $this->userService->create($data);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'User berhasil di buat';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Pengguna',
            'Tambah pengguna baru',
        ];
    }
}
