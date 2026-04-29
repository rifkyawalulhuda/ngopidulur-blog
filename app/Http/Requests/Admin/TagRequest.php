<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tag wajib diisi.',
            'name.string' => 'Nama tag harus berupa teks.',
            'name.max' => 'Nama tag maksimal 255 karakter.',
            'slug.string' => 'Slug tag harus berupa teks.',
            'slug.max' => 'Slug tag maksimal 255 karakter.',
        ];
    }
}
