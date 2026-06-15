<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->components([
                        TextEntry::make('name')
                            ->label('Nama')
                            ->color('gray'),
                        TextEntry::make('email')
                            ->label('Alamat email')
                            ->color('gray'),
                        TextEntry::make('created_at')
                            ->label('Tanggal user dibuat')
                            ->dateTime()
                            ->placeholder('-')
                            ->color('gray'),
                    ]),
            ]);
    }
}
