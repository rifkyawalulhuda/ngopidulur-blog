<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Collection;

class BlogSettings
{
    public static function all(): Collection
    {
        return SiteSetting::query()
            ->where('is_public', true)
            ->pluck('value', 'key');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all()->get($key, $default);
    }

    public static function themeMode(): string
    {
        $theme = strtolower(trim((string) static::get('default_theme', 'light')));

        return in_array($theme, ['dark', 'espresso'], true) ? 'dark' : 'light';
    }
}
