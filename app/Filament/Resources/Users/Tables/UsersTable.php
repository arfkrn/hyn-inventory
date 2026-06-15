<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama'),
                TextColumn::make('email')
                    ->label('Alamat email'),
                TextColumn::make('created_at')
                    ->label('Tanggal akun dibuat'),
                TextColumn::make('roles')
                    ->label('Role')
                    ->getStateUsing(fn ($record) => 
                        $record->getRoleNames()->first()
                    ),
            ])
            ->actions([
                Action::make('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record]))
            ]);
    }
}
