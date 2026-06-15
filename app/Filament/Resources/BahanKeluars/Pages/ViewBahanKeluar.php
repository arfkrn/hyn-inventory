<?php

namespace App\Filament\Resources\BahanKeluars\Pages;

use App\Filament\Resources\BahanKeluars\BahanKeluarResource;
use App\Filament\Resources\BahanKeluars\Schemas\BahanKeluarInfolist;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewBahanKeluar extends ViewRecord
{
    protected static string $resource = BahanKeluarResource::class;

    protected static ?string $title = 'Detail Transaksi';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn ($record) => $record->canBeEdited()) 
        ];
    }

     public function infolist(Schema $schema): Schema
     {
        return BahanKeluarInfolist::configure($schema);
     }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Bahan Keluar',
            'Detail',
        ];
    }
}
