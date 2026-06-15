<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Enums\UserMenuPosition;
use Filament\Actions\Action;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Notifications\Notification;
use App\Support\GlobalAlert;
use Filament\Facades\Filament;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->default()
            ->login()
            // ->brandName('HYN Inventory')
            ->brandLogo(asset('images/Hayuning Inventory.png'))
            ->brandLogoHeight('3.5rem')
            ->favicon(asset('images/Hayuning Inventory favicon.png'))
            ->darkMode(false)
            ->globalSearch(false)
            ->userMenu(position: UserMenuPosition::Sidebar)
            ->spa()
            // ->colors([
            //     'primary' => Color::Slate, // <--- Ini untuk mengatur warna tema seperti di gambar kamu
            // ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->userMenuItems([
                'profile' => fn(Action $action) => $action
                    ->label('Profile')
                    ->url(route('filament.admin.pages.profile'))
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
        ->viteTheme('resources/css/filament/admin/theme.css');
    }

    public function boot(): void
    {

    }
}
