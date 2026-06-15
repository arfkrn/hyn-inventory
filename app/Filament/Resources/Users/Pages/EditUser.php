<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Services\UserService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Edit Pengguna';

    protected UserService $userService;

    public function boot(): void {
        $this->userService = app(UserService::class);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi hapus')
                ->modalDescription('Apakah anda yakin ingin menghapus pengguna ini?.')
                ->modalSubmitActionLabel('Ya, hapus')
                ->modalCancelActionLabel('Batal'),
        ];
    }

    protected function getSaveFormAction(): Action {
        return parent::getSaveFormAction()
            ->label('Simpan perubahan');
    }

    protected function getCancelFormAction(): Action {
        return parent::getCancelFormAction()
            ->label('Batal');
    }


    public function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model 
    {
        return $this->userService->update($record, $data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
