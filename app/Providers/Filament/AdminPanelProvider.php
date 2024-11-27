<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Filament\Navigation\MenuItem;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            // ->brandName("V-GROUP")
            // ->brandLogo(asset("storage/image/logo.png"))
            // ->brandLogoHeight('2rem')
            ->brandLogo(fn() => view('filament.admin.logo'))
            ->favicon(asset('storage/image/logo.png'))
            ->id('admin')
            ->path('web')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Sarabun')
            ->sidebarFullyCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->databaseNotifications()
            ->userMenuItems([
                'edit-profile' => MenuItem::make()
                    // ->label(fn() => auth()->user()->name)
                    ->label('Edit Profile')
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-pencil-square')
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                FilamentEditProfilePlugin::make()
                    ->shouldRegisterNavigation(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowBrowserSessionsForm(false)
                    ->shouldShowEditProfileForm(false)
                    ->shouldShowAvatarForm()
                    ->customProfileComponents([
                        \App\Livewire\CustomProfileComponent::class,
                    ])
            ]);
    }
}
