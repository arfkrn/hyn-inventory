<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Hash;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Profile extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected string $view = 'filament.pages.profile';
    protected static ?string $navigationLabel = 'Profile';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addAvatar')
                ->label('Tambah Foto')
                ->icon('heroicon-o-pencil')
                ->modalHeading('Tambah Foto')
                ->modalSubmitActionLabel('Simpan')
                ->form($this->formSchema())
                ->fillForm(function () {
                    $user = auth()->user();

                    return [
                        'avatar' => $user->avatar
                        ? [$user->avatar]
                        : []
                    ];
                })
                ->action(fn (array $data) => $this->updateProfile($data)),
        ];
    }

    protected function formSchema(): array
    {
        return [
            FileUpload::make('avatar')
                ->label('Foto Profile')
                ->image()
                ->avatar()
                ->directory('avatars')
                ->disk('public')
                ->visibility('public')
                ->multiple(false)
                ->imageEditor()
                ->imageCropAspectRatio('1:1')
                ->maxSize(1024)
                ->previewable(),
        ];
    }

    protected function updateProfile(array $data): void
    {
        $user = auth()->user();

        $user->update([
            'avatar' => $data['avatar'] ?? $user->avatar,
        ]);
    }
}
