<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UniqueSlug
{
    public function for(string $modelClass, string $value, ?int $ignoreId = null, string $column = 'slug'): string
    {
        $base = Str::slug($value);
        $slug = $base !== '' ? $base : Str::lower(Str::random(8));
        $candidate = $slug;
        $suffix = 2;

        while ($this->exists($modelClass, $column, $candidate, $ignoreId)) {
            $candidate = $slug.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function exists(string $modelClass, string $column, string $value, ?int $ignoreId = null): bool
    {
        /** @var class-string<Model> $modelClass */
        $query = $modelClass::query()->where($column, $value);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }
}
