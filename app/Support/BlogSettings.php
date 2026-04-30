<?php

namespace App\Support;

use App\Models\SiteSetting;
use App\Services\SiteSettingsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class BlogSettings
{
    public static function all(): Collection
    {
        return collect(SiteSettingsService::DEFAULTS)->merge(
            SiteSetting::query()
                ->where('is_public', true)
                ->pluck('value', 'key')
                ->all()
        );
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all()->get($key, $default);
    }

    public static function themeMode(?Collection $settings = null): string
    {
        $settings ??= static::all();
        $theme = strtolower(trim((string) $settings->get('default_theme', 'terang')));

        return in_array($theme, ['dark', 'espresso', 'dark_espresso'], true) ? 'dark' : 'light';
    }

    public static function assetUrl(string $key): ?string
    {
        $path = trim((string) static::get($key, ''));

        if ($path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public static function socialLinks(): array
    {
        $decoded = json_decode((string) static::get('social_links', '{}'), true);

        if (! is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->mapWithKeys(fn ($link, $label) => [$label => trim((string) $link)])
            ->filter(fn ($link) => $link !== '')
            ->sortKeys()
            ->all();
    }
}
