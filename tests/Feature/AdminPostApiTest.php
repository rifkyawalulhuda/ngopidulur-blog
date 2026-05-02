<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@ngopidulur.test',
        'name' => 'Admin Ngopi Dulur',
        'role' => 'admin',
        'status' => 'active',
    ]);

    $this->category = Category::create([
        'name' => 'Catatan Harian',
        'slug' => 'catatan-harian',
        'description' => null,
        'is_active' => true,
    ]);

    $this->tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
});

test('admin dapat membuat draft artikel tanpa featured image', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Catatan Pagi',
            'slug' => '',
            'excerpt' => 'Catatan kecil sebelum memulai hari.',
            'content_format' => 'markdown',
            'content' => '# Halo Dunia',
            'category_id' => $this->category->id,
            'tags' => [$this->tag->id],
            'status' => 'draft',
            'is_featured' => false,
            'meta_title' => null,
            'meta_description' => null,
            'published_at' => null,
        ])
        ->assertCreated();

    expect($response->json('item.status'))->toBe('draft');
    expect($response->json('item.slug'))->toBe('catatan-pagi');
    expect($response->json('item.featured_image'))->toBeNull();
});

test('slug artikel otomatis menambah suffix ketika judul duplikat', function () {
    $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Cerita Kopi Pagi',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'markdown',
            'content' => '# Satu',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ])
        ->assertCreated()
        ->assertJsonPath('item.slug', 'cerita-kopi-pagi');

    $second = $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Cerita Kopi Pagi',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'markdown',
            'content' => '# Dua',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ])
        ->assertCreated()
        ->json('item');

    expect($second['slug'])->toBe('cerita-kopi-pagi-2');
});

test('slug manual duplikat ditolak', function () {
    $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Artikel Pertama',
            'slug' => 'slug-milik-saya',
            'excerpt' => null,
            'content_format' => 'markdown',
            'content' => '# Satu',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ])
        ->assertCreated();

    $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Artikel Kedua',
            'slug' => 'slug-milik-saya',
            'excerpt' => null,
            'content_format' => 'markdown',
            'content' => '# Dua',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('upload featured image dikonversi ke webp', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->admin)
        ->post('/admin/api/posts', [
            'title' => 'Artikel Bergambar',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'richtext',
            'content' => '<p>Konten</p>',
            'category_id' => $this->category->id,
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('cover.png', 1200, 800)->size(1200),
        ])
        ->assertCreated();

    $path = $response->json('item.featured_image');

    expect($path)->toEndWith('.webp');
    Storage::disk('public')->assertExists($path);
});

test('upload gambar editor mengembalikan lokasi relatif webp', function () {
    config()->set('app.url', 'http://localhost');
    Storage::fake('public');

    $response = $this->actingAs($this->admin)
        ->post('/admin/api/posts/images', [
            'image' => UploadedFile::fake()->image('inline.png', 1200, 800)->size(900),
        ])
        ->assertOk();

    $path = $response->json('path');

    expect($path)->toStartWith('posts/content/');
    expect($path)->toEndWith('.webp');
    expect($response->json('location'))->toStartWith('/storage/posts/content/');
    Storage::disk('public')->assertExists($path);
});

test('upload gambar editor menolak file non gambar', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->post('/admin/api/posts/images', [
            'image' => UploadedFile::fake()->create('dokumen.pdf', 120, 'application/pdf'),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['image']);
});

test('featured image url memakai path relatif agar tetap cocok dengan host admin aktif', function () {
    config()->set('app.url', 'http://localhost');
    Storage::fake('public');

    $response = $this->actingAs($this->admin)
        ->post('/admin/api/posts', [
            'title' => 'Artikel Host Relatif',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'richtext',
            'content' => '<p>Konten</p>',
            'category_id' => $this->category->id,
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('cover.png', 1200, 800)->size(1200),
        ])
        ->assertCreated();

    expect($response->json('item.featured_image_url'))->toStartWith('/storage/posts/');
    expect($response->json('item.featured_image_url'))->not()->toStartWith('http://localhost');
});

test('featured image wajib saat publish', function () {
    $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Harus Ada Gambar',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'markdown',
            'content' => '# Halo',
            'category_id' => $this->category->id,
            'status' => 'published',
        ])
        ->assertStatus(422)
        ->assertJsonPath('message', 'Gambar unggulan wajib diunggah saat artikel diterbitkan.');
});

test('konten tersanitasi dan preview aman', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Sanitasi Konten',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'markdown',
            'content' => "# Halo\n\n<script>alert('xss')</script>\n\n[Link](javascript:alert('xss'))",
            'category_id' => $this->category->id,
            'status' => 'draft',
        ])
        ->assertCreated();

    expect($response->json('item.rendered_content'))->not()->toContain('<script');
    expect($response->json('item.rendered_content'))->not()->toContain('javascript:');

    $post = Post::where('slug', $response->json('item.slug'))->firstOrFail();

    $this->actingAs($this->admin)
        ->getJson("/admin/api/posts/{$post->slug}/preview")
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('admin dapat publish, archive, dan menghapus artikel', function () {
    Storage::fake('public');

    $create = $this->actingAs($this->admin)
        ->post('/admin/api/posts', [
            'title' => 'Lifecycle Artikel',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'richtext',
            'content' => '<p>Konten</p>',
            'category_id' => $this->category->id,
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('cover.png')->size(800),
        ])
        ->assertCreated()
        ->json('item');

    $this->actingAs($this->admin)
        ->postJson("/admin/api/posts/{$create['slug']}/publish")
        ->assertOk()
        ->assertJsonPath('item.status', 'published');

    $this->actingAs($this->admin)
        ->postJson("/admin/api/posts/{$create['slug']}/archive")
        ->assertOk()
        ->assertJsonPath('item.status', 'archived');

    $this->actingAs($this->admin)
        ->deleteJson("/admin/api/posts/{$create['slug']}")
        ->assertOk()
        ->assertJsonPath('message', 'Artikel berhasil dihapus.');

    $deletedPost = Post::withTrashed()->find($create['id']);

    expect($deletedPost?->trashed())->toBeTrue();
    expect($deletedPost?->slug)->toContain('--trashed-');
});

test('slug artikel yang sudah dihapus dapat dipakai lagi untuk artikel baru', function () {
    $deleted = $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Lorem Ipsum',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'markdown',
            'content' => '# Pertama',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ])
        ->assertCreated()
        ->json('item');

    $this->actingAs($this->admin)
        ->deleteJson("/admin/api/posts/{$deleted['slug']}")
        ->assertOk();

    $recreated = $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', [
            'title' => 'Lorem Ipsum',
            'slug' => '',
            'excerpt' => null,
            'content_format' => 'markdown',
            'content' => '# Kedua',
            'category_id' => $this->category->id,
            'status' => 'draft',
        ])
        ->assertCreated()
        ->json('item');

    expect($recreated['slug'])->toBe('lorem-ipsum');
    expect(Post::withTrashed()->where('id', $deleted['id'])->value('slug'))->toContain('--trashed-');
});

test('public post route hanya menampilkan artikel published', function () {
    $published = Post::create([
        'user_id' => $this->admin->id,
        'category_id' => $this->category->id,
        'title' => 'Artikel Publik',
        'slug' => 'artikel-publik',
        'excerpt' => 'Bisa dibaca publik.',
        'content_format' => 'markdown',
        'content' => '# Publik',
        'rendered_content' => '<h1>Publik</h1>',
        'reading_time_minutes' => 1,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => 'published',
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => now(),
    ]);

    $draft = Post::create([
        'user_id' => $this->admin->id,
        'category_id' => $this->category->id,
        'title' => 'Artikel Draft',
        'slug' => 'artikel-draft',
        'excerpt' => null,
        'content_format' => 'markdown',
        'content' => '# Draft',
        'rendered_content' => '<h1>Draft</h1>',
        'reading_time_minutes' => 1,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => 'draft',
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => null,
    ]);

    $archived = Post::create([
        'user_id' => $this->admin->id,
        'category_id' => $this->category->id,
        'title' => 'Artikel Arsip',
        'slug' => 'artikel-arsip',
        'excerpt' => null,
        'content_format' => 'markdown',
        'content' => '# Arsip',
        'rendered_content' => '<h1>Arsip</h1>',
        'reading_time_minutes' => 1,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => 'archived',
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => now(),
    ]);

    $this->get("/posts/{$published->slug}")
        ->assertOk()
        ->assertSee('Artikel Publik');

    $this->get("/posts/{$draft->slug}")
        ->assertNotFound();

    $this->get("/posts/{$archived->slug}")
        ->assertNotFound();
});
