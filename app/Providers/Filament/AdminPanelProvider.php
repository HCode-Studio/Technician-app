<?php

namespace App\Providers\Filament;

use App\Models\Site;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('')
            ->login()
            ->colors([
                'primary' => '#968F82',
                'secondary' => Color::Amber,
                'background' => Color::Gray,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                
                return $builder->groups([
                    NavigationGroup::make('Sites')
                        ->label('Sites')
                        ->items(
                            array_merge(
                                [NavigationItem::make('Create Site')
                                    ->icon('heroicon-o-plus-circle')
                                    ->url(fn (): string => route('filament.app.resources.sites.create'))
                                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.app.resources.sites.create')),
                                ],
                                Site::where('user_id', auth()->id())->get()->map(function ($site) {
                                    return NavigationItem::make($site->name)
                                        ->icon('heroicon-o-building-office')
                                        ->isActiveWhen(fn (): bool => request()->url() === route('filament.app.resources.sites.edit', $site->id))
                                        ->url(fn (): string => route('filament.app.resources.sites.edit', $site->id))
                                        ->badge(
                                            $site->todos()->where('completed', '!=', 'done')->count(),
                                            function () use ($site){
                                                $todo = $site->todos()->where('completed', '!=', 'done')->count();
                                                switch ($todo) {
                                                    case 0:
                                                        return Color::Green;
                                                    case $todo < 5:
                                                        return Color::Yellow;
                                                    default:
                                                        return Color::Red;
                                                };
                                            }
                                        )
                                    ;
                                })->toArray(),
                            )
                        ),
                ])->items([
                    NavigationItem::make('Dashboard')
                        ->icon('heroicon-o-home')
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.app.pages.dashboard'))
                        ->url(fn (): string => Dashboard::getUrl())
                ]);
            })
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
            ]);
    }
}
