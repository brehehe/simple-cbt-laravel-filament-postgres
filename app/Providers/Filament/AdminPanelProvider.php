<?php

namespace App\Providers\Filament;

use App\Filament\Auth\FilamentLoginIndex;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\{Authenticate, DisableBladeIconComponents, DispatchServingFilamentEvent};
use Filament\{Panel, PanelProvider};
use Filament\Support\Colors\Color;
use Filament\Support\Enums\{MaxWidth, Alignment};
use Filament\Support\Facades\FilamentColor;
use Filament\View\PanelsRenderHook;
use Filament\Pages;
use Illuminate\Cookie\Middleware\{AddQueuedCookiesToResponse, EncryptCookies};
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\{AuthenticateSession, StartSession};
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    private Panel $panel;

    public function panel(Panel $panel): Panel
    {
        $this->panel = $panel;

        return $this
            ->configurePanelSettings()
            ->configureRenderHooks()
            ->configureDiscovery()
            ->configureMiddleware()
            ->configureStyles()
            ->configureFeatures()
            ->configurePlugins()
            ->panel;
    }

    private function configurePanelSettings(): self
    {
        $this->panel
            ->default()
            ->id('admin')
            ->login(FilamentLoginIndex::class)
            // ->registration()
            ->bootUsing(fn () => $this->bootPanelSettings());

        return $this;
    }

    private function bootPanelSettings(): void
    {
        Pages\Page::formActionsAlignment(Alignment::Right);

        FilamentColor::register([
            'slate'   => Color::Slate,
            'teal'    => Color::Teal,
            'fuchsia' => Color::Fuchsia,
            'emerald' => Color::Emerald,
            'lime'    => Color::Lime,
            'pink'    => Color::Pink,
        ]);
    }

    private function configureRenderHooks(): self
    {
        $this->panel
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn () => view('filament.render-hook.footer.filament-footer')
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn () => view('filament.render-hook.topbar-start.filament-topbar-start')
            )
            ->renderHook(
                PanelsRenderHook::SCRIPTS_AFTER,
                fn() => Blade::render('
                    <script>
                        if (!localStorage.getItem("theme")) {
                            localStorage.setItem("theme", "dark");
                        }
                        const updateContent = () => {
                            const updateEmptyActionCells = () => {
                                const emptyCells = document.querySelectorAll("table thead tr th.fi-ta-actions-header-cell:empty");
                                if (!emptyCells.length) return;
                                requestAnimationFrame(() => {
                                    emptyCells.forEach(cell => {
                                        cell.innerHTML = `<div class="px-3 pl-3.5 ps-[23px]"><span class="flex items-center justify-end w-full group gap-x-1 whitespace-nowrap"><span class="text-sm font-semibold fi-ta-header-cell-label text-gray-950 dark:text-white">Aksi</span></span></div>`;
                                    });
                                });
                            };
                            const updateTableHeader = () => {
                                const targetDiv = document.querySelector(".fi-ta-header-ctn .fi-ta-header-toolbar .flex.shrink-0");
                                if (!targetDiv) return;
                                const actionsDiv = targetDiv.querySelector(".fi-ta-actions");
                                if (!actionsDiv && !targetDiv.textContent.trim()) {
                                    const heading = document.querySelector(".fi-header .fi-header-heading");
                                    targetDiv.textContent = `Table ${heading?.textContent.trim() || ""}`;
                                    return;
                                }
                                const updateHeaderText = () => {
                                    [...targetDiv.childNodes]
                                        .filter(node => node !== actionsDiv && node.nodeType === Node.TEXT_NODE)
                                        .forEach(node => node.remove());
                                    if (actionsDiv && getComputedStyle(actionsDiv).display === "none") {
                                        const heading = document.querySelector(".fi-header .fi-header-heading");
                                        const headingText = heading ? `Table ${heading.textContent.trim()}` : "Table";
                                        targetDiv.insertBefore(document.createTextNode(headingText + " "), actionsDiv);
                                    }
                                };
                                if (actionsDiv) {
                                    new MutationObserver(mutations => {
                                        mutations
                                            .filter(mutation => mutation.attributeName === "style")
                                            .forEach(() => updateHeaderText());
                                    }).observe(actionsDiv, {
                                        attributes: true,
                                        attributeFilter: ["style"]
                                    });
                                    updateHeaderText();
                                }
                            };
                            updateEmptyActionCells();
                            updateTableHeader();
                        };
                        document.addEventListener("DOMContentLoaded", updateContent);
                        document.addEventListener("livewire:init", () => {
                            Livewire.hook("morph.updated", () => updateContent());
                            Livewire.on("openNewTab", ({url}) => {
                                window.open(url, "_blank");
                            });
                        });
                    </script>
                ')
            );

        return $this;
    }

    private function configureDiscovery(): self
    {
        $this->panel
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            );

        return $this;
    }

    private function configureMiddleware(): self
    {
        $this->panel
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
            ->authMiddleware([Authenticate::class]);

        return $this;
    }

    private function configureStyles(): self
    {
        $this->panel
            ->colors(['primary' => Color::hex('#0093d7')])
            ->font(
                family: 'Poppins',
                provider: GoogleFontProvider::class
            )
            ->maxContentWidth(MaxWidth::Full)
            ->brandName(Str::upper(cache()->get('system_setting')?->name ?? config('app.name')))
            // ->brandLogo(asset('assets/image/logo.svg'))
            // ->darkModeBrandLogo(asset('assets/image/logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('assets/favicon/android-chrome-512x512.png'))
            ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(false)
            // ->sidebarWidth('40rem')
            ->topNavigation()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->breadcrumbs(false);

        return $this;
    }

    private function configureFeatures(): self
    {
        $this->panel
            ->databaseTransactions()
            ->unsavedChangesAlerts()
            ->databaseNotifications();

        return $this;
    }

    private function configurePlugins(): self
    {
        $this->panel->plugins([
            FilamentShieldPlugin::make(),
        ]);

        return $this;
    }
}
