<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PostPublishingService
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'code', 'div', 'em', 'figcaption', 'figure',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'img', 'li', 'ol', 'p',
        'pre', 'span', 'strong', 'ul',
    ];

    private const ALLOWED_IMAGE_ATTRS = ['alt', 'decoding', 'height', 'loading', 'src', 'title', 'width'];
    private const ALLOWED_LINK_ATTRS = ['href', 'rel', 'target', 'title'];
    private const ALLOWED_GENERIC_ATTRS = ['class'];

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

        $relativePath = 'posts/'.Str::uuid().'.webp';
        $absolutePath = Storage::disk('public')->path($relativePath);
        File::ensureDirectoryExists(dirname($absolutePath));

        if (! imagewebp($image, $absolutePath, 85)) {
            imagedestroy($image);

            throw new RuntimeException('Gagal mengonversi gambar ke WebP.');
        }

        imagedestroy($image);

        return $relativePath;
    }

    public function deleteFeaturedImage(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        Storage::disk('public')->delete($path);
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
        $allowed = self::ALLOWED_GENERIC_ATTRS;

        if ($tagName === 'a') {
            $allowed = self::ALLOWED_LINK_ATTRS;
        } elseif ($tagName === 'img') {
            $allowed = self::ALLOWED_IMAGE_ATTRS;
        }

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
