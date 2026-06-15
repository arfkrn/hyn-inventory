<?php

namespace App\Filament\Resources\StokOpnames\Pages;

use App\Filament\Resources\StokOpnames\Schemas\StokOpnameForm;
use App\Filament\Resources\StokOpnames\StokOpnameResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditStokOpname extends EditRecord
{
    protected static string $resource = StokOpnameResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (! $this->record->canBeEdited()) {
            abort(403);
        }
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
        return StokOpnameForm::configure($schema);
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
