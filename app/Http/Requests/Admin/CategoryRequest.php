<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'slug.string' => 'Slug kategori harus berupa teks.',
            'slug.max' => 'Slug kategori maksimal 255 karakter.',
            'description.string' => 'Deskripsi kategori harus berupa teks.',
            'is_active.boolean' => 'Status kategori harus berupa nilai aktif atau nonaktif.',
        ];
    }
}
