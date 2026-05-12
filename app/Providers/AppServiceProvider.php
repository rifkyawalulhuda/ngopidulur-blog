<?php

namespace App\Providers;

use App\Models\Category;
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
            static $publicNavigationCategories;

            if (! Schema::hasTable('site_settings')) {
                $view->with([
                    'blogSettings' => collect(),
                    'blogTheme' => 'light',
                    'blogSettingAssets' => [],
                    'blogSocialLinks' => [],
                    'publicNavigationCategories' => collect(),
                ]);

                return;
            }

            $blogSettings = BlogSettings::all();

            if ($publicNavigationCategories === null) {
                $publicNavigationCategories = collect();

                if (Schema::hasTable('categories') && Schema::hasTable('posts')) {
                    $publicNavigationCategories = Category::query()
                        ->where('is_active', true)
                        ->whereHas('posts', fn ($query) => $query->published())
                        ->withCount([
                            'posts as published_posts_count' => fn ($query) => $query->published(),
                        ])
                        ->orderByDesc('published_posts_count')
                        ->orderBy('name')
                        ->get();
                }
            }

            $view->with([
                'blogSettings' => $blogSettings,
                'blogTheme' => BlogSettings::themeMode($blogSettings),
                'blogSettingAssets' => [
                    'logo_url' => BlogSettings::assetUrl('logo'),
                    'favicon_url' => BlogSettings::assetUrl('favicon'),
                    'default_og_image_url' => BlogSettings::assetUrl('default_og_image'),
                ],
                'blogSocialLinks' => BlogSettings::socialLinks(),
                'publicNavigationCategories' => $publicNavigationCategories,
            ]);
        });
    }
}
