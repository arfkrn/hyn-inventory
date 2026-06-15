<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Alamat email')
                    ->email()
                    ->required()
                    ->unique('users', 'email'),
                TextInput::make('password')
                    ->password()
                    ->placeholder((fn ($context) => $context === 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : ''))
                    ->required((fn ($context) => $context === 'create')),
                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required(),
            ]);
    }
}
