<?php

namespace App\Http\Requests\Admin;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $post = $this->route('post');
        $postId = $post instanceof Post ? $post->getKey() : null;
        $isPublishing = $this->input('status') === Post::STATUS_PUBLISHED;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'excerpt' => ['nullable', 'string'],
            'content_format' => ['required', Rule::in([Post::CONTENT_FORMAT_RICHTEXT, Post::CONTENT_FORMAT_MARKDOWN])],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'status' => ['required', Rule::in([Post::STATUS_DRAFT, Post::STATUS_PUBLISHED, Post::STATUS_ARCHIVED])],
            'is_featured' => ['nullable', 'boolean'],
            'featured_image' => [
                Rule::requiredIf(fn () => $isPublishing && blank(optional($post)->featured_image)),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'slug.unique' => 'Slug sudah digunakan.',
            'content_format.required' => 'Format konten wajib dipilih.',
            'content_format.in' => 'Format konten harus Visual atau Markdown.',
            'content.required' => 'Konten artikel wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak ditemukan.',
            'tags.array' => 'Tag harus berupa daftar.',
            'tags.*.exists' => 'Salah satu tag yang dipilih tidak ditemukan.',
            'status.required' => 'Status artikel wajib dipilih.',
            'status.in' => 'Status artikel tidak valid.',
            'featured_image.required' => 'Gambar unggulan wajib diunggah saat artikel diterbitkan.',
            'featured_image.required_if' => 'Gambar unggulan wajib diunggah saat artikel diterbitkan.',
            'featured_image.image' => 'File unggulan harus berupa gambar.',
            'featured_image.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WebP.',
            'featured_image.max' => 'Ukuran gambar maksimal 2 MB.',
            'meta_title.max' => 'Meta title maksimal 255 karakter.',
            'featured_image_alt.max' => 'Alt gambar maksimal 255 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->filled('slug') ? trim((string) $this->input('slug')) : null,
            'featured_image_alt' => $this->filled('featured_image_alt') ? trim((string) $this->input('featured_image_alt')) : null,
            'meta_title' => $this->filled('meta_title') ? trim((string) $this->input('meta_title')) : null,
            'meta_description' => $this->filled('meta_description') ? trim((string) $this->input('meta_description')) : null,
        ]);
    }
}
