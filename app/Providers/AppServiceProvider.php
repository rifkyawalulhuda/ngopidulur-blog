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
                    'blogSettingAssets' => [],
                    'blogSocialLinks' => [],
                ]);

                return;
            }

            $blogSettings = BlogSettings::all();

            $view->with([
                'blogSettings' => $blogSettings,
                'blogTheme' => BlogSettings::themeMode($blogSettings),
                'blogSettingAssets' => [
                    'logo_url' => BlogSettings::assetUrl('logo'),
                    'favicon_url' => BlogSettings::assetUrl('favicon'),
                    'default_og_image_url' => BlogSettings::assetUrl('default_og_image'),
                ],
                'blogSocialLinks' => BlogSettings::socialLinks(),
            ]);
        });
    }
}
