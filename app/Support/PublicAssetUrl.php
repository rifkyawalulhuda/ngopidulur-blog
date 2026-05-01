<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class PublicAssetUrl
{
    public static function fromPublicDisk(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        $url = Storage::disk('public')->url($path);
        $parsedPath = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);

        if (! is_string($parsedPath) || $parsedPath === '') {
            return $url;
        }

        return $query ? $parsedPath.'?'.$query : $parsedPath;
    }
}
