<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Support\PublicAssetUrl;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class SiteSettingsService
{
    public const DEFAULTS = [
        'site_name' => 'Ngopi Dulur',
        'site_tagline' => 'Warm Coffee Meets Modern Tech',
        'site_description' => 'Blog pribadi hangat untuk catatan, ide, dan tulisan santai.',
        'logo' => null,
        'favicon' => null,
        'default_meta_title' => 'Ngopi Dulur',
        'default_meta_description' => 'Personal blog CMS dengan nuansa hangat dan fondasi modern.',
        'default_og_image' => null,
        'footer_note' => 'Dibuat dengan Laravel, Vue, dan secangkir kopi yang pelan-pelan habis.',
        'social_links' => '{}',
        'hero_badge' => 'Ngopi Dulur',
        'hero_heading' => 'Cerita, catatan, dan pikiran ringan yang enak dibaca sambil ngopi.',
        'hero_subheading' => 'Seduh bacaan terbaru dari ruang tulis pribadi yang modern dan hangat.',
        'hero_cta_text' => 'Cari artikel',
        'default_theme' => 'terang',
    ];

    private const PUBLIC_KEYS = [
        'site_name',
        'site_tagline',
        'site_description',
        'logo',
        'favicon',
        'default_meta_title',
        'default_meta_description',
        'default_og_image',
        'footer_note',
        'social_links',
        'hero_badge',
        'hero_heading',
        'hero_subheading',
        'hero_cta_text',
        'default_theme',
    ];

    public function all(): Collection
    {
        return collect(self::DEFAULTS)->merge(
            SiteSetting::query()->pluck('value', 'key')->all()
        );
    }

    public function publicSettings(): Collection
    {
        return collect(self::DEFAULTS)->merge(
            SiteSetting::query()
                ->where('is_public', true)
                ->pluck('value', 'key')
                ->all()
        );
    }

    public function payload(): array
    {
        $settings = $this->all();

        return [
            'site_name' => (string) $settings->get('site_name', self::DEFAULTS['site_name']),
            'site_tagline' => (string) $settings->get('site_tagline', self::DEFAULTS['site_tagline']),
            'site_description' => (string) $settings->get('site_description', self::DEFAULTS['site_description']),
            'logo' => $settings->get('logo'),
            'logo_url' => $this->assetUrl($settings->get('logo')),
            'favicon' => $settings->get('favicon'),
            'favicon_url' => $this->assetUrl($settings->get('favicon')),
            'default_meta_title' => (string) $settings->get('default_meta_title', self::DEFAULTS['default_meta_title']),
            'default_meta_description' => (string) $settings->get('default_meta_description', self::DEFAULTS['default_meta_description']),
            'default_og_image' => $settings->get('default_og_image'),
            'default_og_image_url' => $this->assetUrl($settings->get('default_og_image')),
            'footer_note' => (string) $settings->get('footer_note', self::DEFAULTS['footer_note']),
            'social_links' => $this->decodeSocialLinks($settings->get('social_links')),
            'hero_badge' => (string) $settings->get('hero_badge', self::DEFAULTS['hero_badge']),
            'hero_heading' => (string) $settings->get('hero_heading', self::DEFAULTS['hero_heading']),
            'hero_subheading' => (string) $settings->get('hero_subheading', self::DEFAULTS['hero_subheading']),
            'hero_cta_text' => (string) $settings->get('hero_cta_text', self::DEFAULTS['hero_cta_text']),
            'default_theme' => $this->normalizeTheme((string) $settings->get('default_theme', self::DEFAULTS['default_theme'])),
        ];
    }

    public function update(array $data): array
    {
        $settings = $this->all();

        $this->persistValue('site_name', $data['site_name'] ?? $settings->get('site_name'));
        $this->persistValue('site_tagline', $data['site_tagline'] ?? $settings->get('site_tagline'));
        $this->persistValue('site_description', $data['site_description'] ?? $settings->get('site_description'));
        $this->persistValue('default_meta_title', $data['default_meta_title'] ?? $settings->get('default_meta_title'));
        $this->persistValue('default_meta_description', $data['default_meta_description'] ?? $settings->get('default_meta_description'));
        $this->persistValue('footer_note', $data['footer_note'] ?? $settings->get('footer_note'));
        $this->persistValue('hero_badge', $data['hero_badge'] ?? $settings->get('hero_badge'));
        $this->persistValue('hero_heading', $data['hero_heading'] ?? $settings->get('hero_heading'));
        $this->persistValue('hero_subheading', $data['hero_subheading'] ?? $settings->get('hero_subheading'));
        $this->persistValue('hero_cta_text', $data['hero_cta_text'] ?? $settings->get('hero_cta_text'));
        $this->persistValue('default_theme', $data['default_theme'] ?? $settings->get('default_theme'));
        $this->persistValue('social_links', json_encode($this->normalizeSocialLinks($data['social_links'] ?? []), JSON_UNESCAPED_SLASHES));

        $this->handleImageSetting(
            key: 'logo',
            directory: 'settings/logo',
            currentPath: $settings->get('logo'),
            uploadedFile: $data['logo'] ?? null,
            remove: (bool) ($data['remove_logo'] ?? false),
            convertToWebp: true,
        );

        $this->handleImageSetting(
            key: 'favicon',
            directory: 'settings/favicon',
            currentPath: $settings->get('favicon'),
            uploadedFile: $data['favicon'] ?? null,
            remove: (bool) ($data['remove_favicon'] ?? false),
            convertToWebp: false,
        );

        $this->handleImageSetting(
            key: 'default_og_image',
            directory: 'settings/og',
            currentPath: $settings->get('default_og_image'),
            uploadedFile: $data['default_og_image'] ?? null,
            remove: (bool) ($data['remove_default_og_image'] ?? false),
            convertToWebp: true,
        );

        return $this->payload();
    }

    public function assetUrl(mixed $path): ?string
    {
        return PublicAssetUrl::fromPublicDisk((string) $path);
    }

    public function decodeSocialLinks(mixed $value): array
    {
        if (is_array($value)) {
            return $this->normalizeSocialLinks($value);
        }

        $decoded = json_decode((string) $value, true);

        return $this->normalizeSocialLinks(is_array($decoded) ? $decoded : []);
    }

    private function normalizeSocialLinks(array $links): array
    {
        return collect($links)
            ->mapWithKeys(fn ($value, $key) => [$key => trim((string) $value)])
            ->filter(fn ($value) => $value !== '')
            ->sortKeys()
            ->all();
    }

    private function handleImageSetting(
        string $key,
        string $directory,
        mixed $currentPath,
        ?UploadedFile $uploadedFile,
        bool $remove,
        bool $convertToWebp,
    ): void {
        $currentPath = $currentPath ? (string) $currentPath : null;

        if ($remove && $currentPath) {
            Storage::disk('public')->delete($currentPath);
            $this->persistValue($key, null);
            $currentPath = null;
        }

        if (! $uploadedFile) {
            return;
        }

        if ($currentPath) {
            Storage::disk('public')->delete($currentPath);
        }

        $path = $convertToWebp
            ? $this->storeAsWebp($uploadedFile, $directory)
            : $uploadedFile->store($directory, 'public');

        $this->persistValue($key, $path);
    }

    private function storeAsWebp(UploadedFile $file, string $directory): string
    {
        $binary = file_get_contents($file->getRealPath());

        if ($binary === false) {
            throw new RuntimeException('Gagal membaca file gambar.');
        }

        $image = imagecreatefromstring($binary);

        if ($image === false) {
            throw new RuntimeException('File gambar tidak valid.');
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $relativePath = trim($directory, '/').'/'.Str::uuid().'.webp';
        $absolutePath = Storage::disk('public')->path($relativePath);
        File::ensureDirectoryExists(dirname($absolutePath));

        if (! imagewebp($image, $absolutePath, 85)) {
            imagedestroy($image);

            throw new RuntimeException('Gagal mengonversi gambar ke WebP.');
        }

        imagedestroy($image);

        return $relativePath;
    }

    private function persistValue(string $key, mixed $value): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'is_public' => in_array($key, self::PUBLIC_KEYS, true),
            ],
        );
    }

    private function normalizeTheme(string $value): string
    {
        $value = strtolower(trim($value));

        return in_array($value, ['dark', 'espresso', 'dark_espresso'], true) ? 'dark_espresso' : 'terang';
    }
}
