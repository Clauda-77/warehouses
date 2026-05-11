<?php

namespace App\Providers\Filament;

use App\Enums\UserRole;
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
use Filament\Navigation\MenuItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            //       ->homeUrl(fn (): string => match (auth()->user()?->role) {
            //     UserRole::ADMIN => '/admin',
            //     UserRole::MONITOR => '/monitor/dashboard',
            //     UserRole::WAREHOUSE_KEEPER => '/warehouse/dashboard',
            //     default => '/',
            // })
            ->colors([
                'primary' => Color::Blue,
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'success' => Color::Green,
                'warning' => Color::Amber,
                // 'primary' => Color::Amber,
            ])
            ->topNavigation()
            ->brandLogo(asset('images/loogoo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/loogoo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            // ->font('Inter')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                // FilamentInfoWidget::class,
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


            ->font('Cairo')
            // ->renderHook(
            //     'panels::body.start',
            //     fn (): string => '<div dir="rtl">',
            // )
            // ->renderHook(
            //     'panels::body.end',
            //     fn (): string => '</div>',
            // );

            ->renderHook(
                'panels::head.start',
                fn(): string => '
                <style>
                    :root {
                        direction: rtl;
                        font-family: "Cairo", sans-serif;
                    }
                    body {
                        text-align: right;
                        font-family: "Cairo", sans-serif;
                    }
                    .fi-btn {
                        flex-direction: row-reverse;
                        gap: 0.5rem;
                    }
                    .fi-sidebar-nav {
                        text-align: right;
                    }
                    .fi-dropdown-list {
                        text-align: right;
                    }
                    .fi-table-header-cell {
                        text-align: right;
                    }
                    .fi-input-wrapper {
                        text-align: right;
                    }
                    .fi-modal-header {
                        text-align: right;
                    }
                    .fi-modal-content {
                        text-align: right;
                    }
                    .fi-modal-footer {
                        justify-content: flex-start;
                        flex-direction: row-reverse;
                    }
                </style>
            ',
            );
    }
    //     ->brandLogo(asset('images/logo.png'))
    //     ->brandLogoHeight('40px')
    //     ->favicon(asset('images/favicon.png'))
    //    ->viteTheme('resources/css/filament/admin/theme.css')



}
