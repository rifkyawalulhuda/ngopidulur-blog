<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PostPublishingService
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'caption', 'code', 'col', 'colgroup', 'div',
        'em', 'figcaption', 'figure', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr',
        'i', 'img', 'li', 'ol', 'p', 'pre', 's', 'span', 'strong', 'sub', 'sup',
        'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'u', 'ul',
    ];

    private const ALLOWED_GENERIC_ATTRS = ['class', 'style'];
    private const ALLOWED_IMAGE_ATTRS = ['alt', 'class', 'decoding', 'height', 'loading', 'src', 'style', 'title', 'width'];
    private const ALLOWED_LINK_ATTRS = ['class', 'href', 'rel', 'target', 'title'];
    private const ALLOWED_TABLE_CELL_ATTRS = ['class', 'colspan', 'rowspan', 'scope', 'style'];
    private const ALLOWED_TABLE_ATTRS = ['class', 'style'];
    private const ALLOWED_TABLE_COLUMN_ATTRS = ['class', 'span', 'style', 'width'];
    private const ALLOWED_STYLE_RULES = [
        'display' => '/^(block|inline|inline-block)$/i',
        'float' => '/^(left|right|none)$/i',
        'height' => '/^(auto|0|[0-9]+(?:\.[0-9]+)?(?:px|%|rem|em|vh))$/i',
        'margin-left' => '/^(auto|0|[0-9]+(?:\.[0-9]+)?(?:px|%|rem|em))$/i',
        'margin-right' => '/^(auto|0|[0-9]+(?:\.[0-9]+)?(?:px|%|rem|em))$/i',
        'text-align' => '/^(left|right|center|justify)$/i',
        'width' => '/^(auto|0|[0-9]+(?:\.[0-9]+)?(?:px|%|rem|em|vw))$/i',
    ];

    public function renderContent(string $format, string $content): string
    {
        $format = $format === 'markdown' ? 'markdown' : 'richtext';

        $html = $format === 'markdown'
            ? Str::markdown($content, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ])
            : $content;

        return $this->sanitizeHtml($html);
    }

    public function estimateReadingTime(string $renderedHtml): int
    {
        $plainText = html_entity_decode(strip_tags($renderedHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainText = trim(preg_replace('/\s+/u', ' ', $plainText) ?? '');

        if ($plainText === '') {
            return 1;
        }

        preg_match_all('/[\p{L}\p{N}\']+/u', $plainText, $matches);
        $words = count($matches[0]);

        return max(1, (int) ceil($words / 220));
    }

    public function storeFeaturedImage(UploadedFile $file): string
    {
        return $this->storeImageAsWebp($file, 'posts');
    }

    public function storeContentImage(UploadedFile $file): string
    {
        return $this->storeImageAsWebp($file, 'posts/content');
    }

    public function deleteFeaturedImage(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function storeImageAsWebp(UploadedFile $file, string $directory): string
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
        $targetDirectory = dirname($absolutePath);

        if (! is_dir($targetDirectory) && ! mkdir($targetDirectory, 0755, true) && ! is_dir($targetDirectory)) {
            imagedestroy($image);

            throw new RuntimeException('Gagal menyiapkan folder gambar.');
        }

        if (! imagewebp($image, $absolutePath, 85)) {
            imagedestroy($image);

            throw new RuntimeException('Gagal mengonversi gambar ke WebP.');
        }

        imagedestroy($image);

        return $relativePath;
    }

    private function sanitizeHtml(string $html): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        $dom->loadHTML(
            '<?xml encoding="utf-8"?><!doctype html><html><body><div id="ngopi-dulur-root">'.$html.'</div></body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $dom->getElementById('ngopi-dulur-root');

        if ($root === null) {
            return e($html);
        }

        $this->walkAndSanitize($dom, $root);

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return $output;
    }

    private function walkAndSanitize(\DOMDocument $dom, \DOMNode $node): void
    {
        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
            $child = $node->childNodes->item($i);

            if ($child === null) {
                continue;
            }

            if ($child->nodeType === XML_TEXT_NODE) {
                continue;
            }

            if ($child->nodeType !== XML_ELEMENT_NODE) {
                $node->removeChild($child);
                continue;
            }

            /** @var \DOMElement $child */
            $tagName = strtolower($child->tagName);

            if (! in_array($tagName, self::ALLOWED_TAGS, true)) {
                if ($child->hasChildNodes()) {
                    $this->unwrapNode($dom, $node, $child);
                } else {
                    $node->removeChild($child);
                }

                continue;
            }

            $this->sanitizeAttributes($child, $tagName);
            $this->walkAndSanitize($dom, $child);
        }
    }

    private function unwrapNode(\DOMDocument $dom, \DOMNode $parent, \DOMNode $child): void
    {
        $fragment = $dom->createDocumentFragment();

        while ($child->firstChild !== null) {
            $fragment->appendChild($child->firstChild);
        }

        $parent->replaceChild($fragment, $child);
    }

    private function sanitizeAttributes(\DOMElement $element, string $tagName): void
    {
        $allowed = $this->allowedAttributesForTag($tagName);

        foreach (iterator_to_array($element->attributes ?? []) as $attribute) {
            $name = strtolower($attribute->nodeName);

            if (! in_array($name, $allowed, true)) {
                $element->removeAttributeNode($attribute);
                continue;
            }

            if (($name === 'href' || $name === 'src') && ! $this->isSafeUrl($attribute->nodeValue)) {
                $element->removeAttribute($name);
                continue;
            }

            if ($name === 'target' && ! in_array(strtolower($attribute->nodeValue), ['_blank', '_self', '_parent', '_top'], true)) {
                $element->removeAttribute($name);
                continue;
            }

            if ($name === 'style') {
                $sanitizedStyle = $this->sanitizeStyleValue($attribute->nodeValue);

                if ($sanitizedStyle === null) {
                    $element->removeAttribute($name);
                    continue;
                }

                $element->setAttribute('style', $sanitizedStyle);
            }

            if (str_starts_with($name, 'on')) {
                $element->removeAttribute($name);
            }
        }

        if ($tagName === 'a') {
            $element->setAttribute('rel', 'noreferrer noopener');
        }

        if ($tagName === 'img') {
            $element->setAttribute('loading', $element->getAttribute('loading') ?: 'lazy');
            $element->setAttribute('decoding', $element->getAttribute('decoding') ?: 'async');

            if ($element->getAttribute('alt') === '') {
                $element->setAttribute('alt', '');
            }
        }
    }

    private function allowedAttributesForTag(string $tagName): array
    {
        return match ($tagName) {
            'a' => self::ALLOWED_LINK_ATTRS,
            'img' => self::ALLOWED_IMAGE_ATTRS,
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'figure', 'figcaption', 'p', 'div', 'span', 'blockquote', 'pre', 'code', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li' => self::ALLOWED_GENERIC_ATTRS,
            'th', 'td' => self::ALLOWED_TABLE_CELL_ATTRS,
            'col', 'colgroup' => self::ALLOWED_TABLE_COLUMN_ATTRS,
            default => [],
        };
    }

    private function sanitizeStyleValue(string $style): ?string
    {
        $declarations = array_filter(array_map('trim', explode(';', $style)));
        $allowedDeclarations = [];

        foreach ($declarations as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);

            $rule = self::ALLOWED_STYLE_RULES[$property] ?? null;

            if ($rule === null || ! preg_match($rule, $value)) {
                continue;
            }

            $allowedDeclarations[$property] = $property.':'.$value;
        }

        if ($allowedDeclarations === []) {
            return null;
        }

        return implode('; ', array_values($allowedDeclarations));
    }

    private function isSafeUrl(string $value): bool
    {
        $value = trim($value);

        if ($value === '' || str_starts_with($value, '#') || str_starts_with($value, '/')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }
}
