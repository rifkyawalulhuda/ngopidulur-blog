<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class SettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'site_tagline' => ['required', 'string', 'max:180'],
            'site_description' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', File::types(['jpg', 'jpeg', 'png', 'webp'])->max(2048)],
            'favicon' => ['nullable', File::types(['ico', 'png', 'jpg', 'jpeg', 'webp'])->max(2048)],
            'default_meta_title' => ['required', 'string', 'max:180'],
            'default_meta_description' => ['required', 'string', 'max:255'],
            'default_og_image' => ['nullable', File::types(['jpg', 'jpeg', 'png', 'webp'])->max(2048)],
            'footer_note' => ['required', 'string', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url', 'max:255'],
            'hero_badge' => ['required', 'string', 'max:80'],
            'hero_heading' => ['required', 'string', 'max:180'],
            'hero_subheading' => ['required', 'string', 'max:280'],
            'hero_cta_text' => ['required', 'string', 'max:60'],
            'default_theme' => ['required', Rule::in(['terang', 'dark_espresso'])],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
            'remove_default_og_image' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'site_name.required' => 'Nama situs wajib diisi.',
            'site_tagline.required' => 'Tagline wajib diisi.',
            'site_description.required' => 'Deskripsi situs wajib diisi.',
            'default_meta_title.required' => 'SEO title default wajib diisi.',
            'default_meta_description.required' => 'SEO description default wajib diisi.',
            'footer_note.required' => 'Teks footer wajib diisi.',
            'hero_badge.required' => 'Badge hero wajib diisi.',
            'hero_heading.required' => 'Judul hero wajib diisi.',
            'hero_subheading.required' => 'Subtitle hero wajib diisi.',
            'hero_cta_text.required' => 'Teks CTA hero wajib diisi.',
            'default_theme.required' => 'Tema default wajib dipilih.',
            'default_theme.in' => 'Tema default yang dipilih tidak valid.',
            'social_links.*.url' => 'Setiap tautan sosial harus berupa URL yang valid.',
            'logo.max' => 'Logo maksimal 2 MB.',
            'favicon.max' => 'Favicon maksimal 2 MB.',
            'default_og_image.max' => 'Gambar Open Graph maksimal 2 MB.',
        ];
    }
}
