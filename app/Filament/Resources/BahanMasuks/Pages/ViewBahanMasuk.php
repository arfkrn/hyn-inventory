<?php

namespace App\Filament\Resources\BahanMasuks\Pages;

use App\Filament\Resources\BahanMasuks\BahanMasukResource;
use App\Filament\Resources\BahanMasuks\Schemas\BahanMasukInfolist;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewBahanMasuk extends ViewRecord
{
    protected static string $resource = BahanMasukResource::class;

    protected static ?string $title = 'Detail Transaksi';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn ($record) => $record->canBeEdited()),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return BahanMasukInfolist::configure($schema);
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::getResource()::getUrl('index') => 'Bahan Masuk',
            'Detail',
        ];
    }
}
