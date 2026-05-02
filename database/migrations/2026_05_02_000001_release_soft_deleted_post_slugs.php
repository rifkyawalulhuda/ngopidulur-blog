<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('posts')
            ->select(['id', 'slug'])
            ->whereNotNull('deleted_at')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $post): void {
                $suffix = '--trashed-'.$post->id;
                $base = Str::limit((string) $post->slug, 255 - strlen($suffix), '');
                $trashedSlug = $base.$suffix;

                if ($post->slug === $trashedSlug) {
                    return;
                }

                DB::table('posts')
                    ->where('id', $post->id)
                    ->update(['slug' => $trashedSlug]);
            });
    }

    public function down(): void
    {
        // Historical deleted slugs are intentionally not restored.
    }
};
