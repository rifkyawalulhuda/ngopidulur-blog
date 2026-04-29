<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@ngopidulur.test',
        'name' => 'Admin Ngopi Dulur',
        'role' => 'admin',
        'status' => 'active',
    ]);
});

test('dashboard stats menampilkan ringkasan konten', function () {
    $category = Category::create([
        'name' => 'Catatan Harian',
        'slug' => 'catatan-harian',
        'description' => null,
        'is_active' => true,
    ]);

    Post::create([
        'user_id' => $this->admin->id,
        'category_id' => $category->id,
        'title' => 'Tulisan Terbaru',
        'slug' => 'tulisan-terbaru',
        'excerpt' => 'Ringkasan',
        'content_format' => 'markdown',
        'content' => '# Halo',
        'rendered_content' => '<h1>Halo</h1>',
        'reading_time_minutes' => 2,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => 'published',
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => now()->subDay(),
    ]);

    Post::create([
        'user_id' => $this->admin->id,
        'category_id' => $category->id,
        'title' => 'Draft Masih Oke',
        'slug' => 'draft-masih-oke',
        'excerpt' => null,
        'content_format' => 'richtext',
        'content' => '<p>Draft</p>',
        'rendered_content' => '<p>Draft</p>',
        'reading_time_minutes' => 1,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => 'draft',
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => now()->subHours(2),
    ]);

    Post::create([
        'user_id' => $this->admin->id,
        'category_id' => $category->id,
        'title' => 'Arsip Lama',
        'slug' => 'arsip-lama',
        'excerpt' => null,
        'content_format' => 'richtext',
        'content' => '<p>Arsip</p>',
        'rendered_content' => '<p>Arsip</p>',
        'reading_time_minutes' => 1,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => 'archived',
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => now(),
    ]);

    $this->actingAs($this->admin)
        ->getJson('/admin/api/dashboard')
        ->assertOk()
        ->assertJsonPath('stats.total_posts', 3)
        ->assertJsonPath('stats.published_posts', 1)
        ->assertJsonPath('stats.draft_posts', 1)
        ->assertJsonPath('stats.archived_posts', 1)
        ->assertJsonPath('stats.total_categories', 1)
        ->assertJsonPath('stats.total_tags', 0)
        ->assertJsonPath('latest_posts.0.title', 'Arsip Lama');
});

test('kategori dapat dibuat dan slug otomatis unik', function () {
    $this->actingAs($this->admin)
        ->postJson('/admin/api/categories', [
            'name' => 'Catatan Harian',
            'slug' => '',
            'description' => 'Harapan dan kebiasaan kecil.',
            'is_active' => true,
        ])
        ->assertCreated()
        ->assertJsonPath('item.slug', 'catatan-harian');

    $second = $this->actingAs($this->admin)
        ->postJson('/admin/api/categories', [
            'name' => 'Catatan Harian',
            'slug' => '',
            'description' => 'Versi kedua.',
            'is_active' => true,
        ])
        ->assertCreated()
        ->json('item');

    expect($second['slug'])->toBe('catatan-harian-2');
});

test('kategori yang masih dipakai artikel tidak bisa dihapus', function () {
    $category = Category::create([
        'name' => 'Teknologi',
        'slug' => 'teknologi',
        'description' => null,
        'is_active' => true,
    ]);

    Post::create([
        'user_id' => $this->admin->id,
        'category_id' => $category->id,
        'title' => 'Artikel Terkait',
        'slug' => 'artikel-terkait',
        'excerpt' => null,
        'content_format' => 'markdown',
        'content' => 'Konten',
        'rendered_content' => '<p>Konten</p>',
        'reading_time_minutes' => 1,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => 'published',
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => now(),
    ]);

    $this->actingAs($this->admin)
        ->deleteJson("/admin/api/categories/{$category->slug}")
        ->assertStatus(422)
        ->assertJsonPath('message', 'Kategori tidak bisa dihapus karena masih dipakai oleh artikel.');
});

test('tag dapat dibuat dan slug otomatis unik', function () {
    $this->actingAs($this->admin)
        ->postJson('/admin/api/tags', [
            'name' => 'Laravel',
            'slug' => '',
        ])
        ->assertCreated()
        ->assertJsonPath('item.slug', 'laravel');

    $second = $this->actingAs($this->admin)
        ->postJson('/admin/api/tags', [
            'name' => 'Laravel',
            'slug' => '',
        ])
        ->assertCreated()
        ->json('item');

    expect($second['slug'])->toBe('laravel-2');
});

test('tag dapat dihapus dan pivot ikut dibersihkan', function () {
    $category = Category::create([
        'name' => 'Teknologi',
        'slug' => 'teknologi',
        'description' => null,
        'is_active' => true,
    ]);

    $tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $post = Post::create([
        'user_id' => $this->admin->id,
        'category_id' => $category->id,
        'title' => 'Artikel Tag',
        'slug' => 'artikel-tag',
        'excerpt' => null,
        'content_format' => 'markdown',
        'content' => 'Konten',
        'rendered_content' => '<p>Konten</p>',
        'reading_time_minutes' => 1,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => 'published',
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => now(),
    ]);

    $post->tags()->attach($tag->id);

    $this->actingAs($this->admin)
        ->deleteJson("/admin/api/tags/{$tag->slug}")
        ->assertOk()
        ->assertJsonPath('message', 'Tag berhasil dihapus.');

    expect($post->fresh()->tags)->toHaveCount(0);
    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
});
