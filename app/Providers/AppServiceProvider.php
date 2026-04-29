<?php

namespace App\Providers;

use App\Support\BlogSettings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (! Schema::hasTable('site_settings')) {
                $view->with([
                    'blogSettings' => collect(),
                    'blogTheme' => 'light',
                ]);

                return;
            }

            $blogSettings = BlogSettings::all();

            $view->with([
                'blogSettings' => $blogSettings,
                'blogTheme' => in_array(strtolower(trim((string) $blogSettings->get('default_theme', 'light'))), ['dark', 'espresso'], true) ? 'dark' : 'light',
            ]);
        });
    }
}
