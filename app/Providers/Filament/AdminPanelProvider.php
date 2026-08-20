<?php

namespace App\Providers\Filament;

use App\Filament\Pages\PlafondAnggaran;
use App\Http\Middleware\TempAutoLogin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsIconAlias;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName('Finance')
            ->globalSearch(false)
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::TOPBAR_LOGO_AFTER,
                fn (): string => view('filament.topbar.page-title')->render(),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => view('filament.topbar.periode-aktif')->render(),
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                PlafondAnggaran::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->errorNotifications(true)
            ->registerErrorNotification(
                title: 'Terjadi Kesalahan',
                body: 'Terjadi kesalahan saat memuat data. Silakan coba lagi atau hubungi admin.',
            )
            ->registerErrorNotification(
                title: 'Data Tidak Ditemukan',
                body: 'Data yang dicari tidak ditemukan dalam sistem.',
                statusCode: 404,
            )
            ->registerErrorNotification(
                title: 'Akses Ditolak',
                body: 'Anda tidak memiliki akses untuk melakukan ini.',
                statusCode: 403,
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                TempAutoLogin::class,
            ]);
    }

    public function boot(): void
    {
        FilamentIcon::register([
            PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON => Heroicon::OutlinedBars3,
            PanelsIconAlias::SIDEBAR_EXPAND_BUTTON => Heroicon::OutlinedBars3,
        ]);
    }
}
