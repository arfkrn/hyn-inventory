<?php

namespace App\Filament\Resources\StokOpnames\Pages;

use App\Filament\Resources\StokOpnames\Schemas\StokOpnameInfolist;
use App\Filament\Resources\StokOpnames\StokOpnameResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewStokOpname extends ViewRecord
{
    protected static string $resource = StokOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn ($record) => $record->canBeEdited())
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return StokOpnameInfolist::configure($schema);
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Stok Opname',
            'Detail',
        ];
    }
}
