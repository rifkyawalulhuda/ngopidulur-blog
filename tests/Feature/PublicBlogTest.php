<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\SiteSetting;
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

    $this->categoryCoffee = Category::create([
        'name' => 'Ngopi & Santai',
        'slug' => 'ngopi-santai',
        'description' => 'Tulisan santai sambil ngopi.',
        'is_active' => true,
    ]);

    $this->categoryTech = Category::create([
        'name' => 'Teknologi',
        'slug' => 'teknologi',
        'description' => 'Catatan teknis ringan.',
        'is_active' => true,
    ]);

    $this->tagLaravel = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    collect([
        'site_name' => 'Ngopi Dulur',
        'site_tagline' => 'Warm Coffee Meets Modern Tech',
        'site_description' => 'Blog pribadi hangat untuk catatan, ide, dan tulisan santai.',
        'default_theme' => 'light',
        'hero_badge' => 'Ngopi Dulur',
        'hero_heading' => 'Cerita, catatan, dan pikiran ringan yang enak dibaca sambil ngopi.',
        'hero_subheading' => 'Seduh bacaan terbaru dari ruang tulis pribadi yang modern dan hangat.',
        'footer_note' => 'Dibuat dengan Laravel, Vue, dan secangkir kopi yang pelan-pelan habis.',
        'default_meta_description' => 'Personal blog CMS dengan nuansa hangat dan fondasi modern.',
    ])->each(function (string $value, string $key) {
        SiteSetting::updateOrCreate([
            'key' => $key,
        ], [
            'value' => $value,
            'is_public' => true,
        ]);
    });
});

function makePost(array $overrides = []): Post
{
    return Post::create(array_merge([
        'user_id' => User::firstOrFail()->id,
        'category_id' => Category::firstOrFail()->id,
        'title' => 'Artikel Ngopi',
        'slug' => 'artikel-ngopi-'.str()->random(5),
        'excerpt' => 'Ringkasan singkat artikel.',
        'content_format' => 'markdown',
        'content' => '# Halo',
        'rendered_content' => '<h1>Halo</h1>',
        'reading_time_minutes' => 2,
        'featured_image' => null,
        'featured_image_alt' => null,
        'is_featured' => false,
        'status' => Post::STATUS_PUBLISHED,
        'meta_title' => null,
        'meta_description' => null,
        'published_at' => now(),
    ], $overrides));
}

test('homepage menampilkan hanya artikel published dan featured fallback dari artikel terbaru', function () {
    $featured = makePost([
        'title' => 'Kopi Pagi',
        'slug' => 'kopi-pagi',
        'is_featured' => true,
        'category_id' => $this->categoryCoffee->id,
        'published_at' => now()->subDay(),
    ]);

    $latest = makePost([
        'title' => 'Catatan Sore',
        'slug' => 'catatan-sore',
        'category_id' => $this->categoryTech->id,
        'published_at' => now(),
    ]);

    $draft = makePost([
        'title' => 'Draft Tidak Muncul',
        'slug' => 'draft-tidak-muncul',
        'status' => Post::STATUS_DRAFT,
        'published_at' => null,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Kopi Pagi')
        ->assertSee('Catatan Sore')
        ->assertDontSee('Draft Tidak Muncul')
        ->assertSee('Artikel unggulan');
});

test('homepage memakai artikel terbaru sebagai fallback ketika tidak ada featured', function () {
    $older = makePost([
        'title' => 'Catatan Lama',
        'slug' => 'catatan-lama',
        'category_id' => $this->categoryCoffee->id,
        'published_at' => now()->subDays(2),
    ]);

    $newer = makePost([
        'title' => 'Catatan Baru',
        'slug' => 'catatan-baru',
        'category_id' => $this->categoryTech->id,
        'published_at' => now(),
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Catatan Baru')
        ->assertSee('Catatan Lama');
});

test('post publik yang published bisa dibuka dan draft atau archived memunculkan 404', function () {
    $published = makePost([
        'title' => 'Artikel Publik',
        'slug' => 'artikel-publik',
        'category_id' => $this->categoryCoffee->id,
        'published_at' => now(),
    ]);

    $related = makePost([
        'title' => 'Artikel Terkait',
        'slug' => 'artikel-terkait',
        'category_id' => $this->categoryCoffee->id,
        'published_at' => now()->subHour(),
    ]);

    $draft = makePost([
        'title' => 'Artikel Draft',
        'slug' => 'artikel-draft',
        'status' => Post::STATUS_DRAFT,
        'published_at' => null,
    ]);

    $archived = makePost([
        'title' => 'Artikel Arsip',
        'slug' => 'artikel-arsip',
        'status' => Post::STATUS_ARCHIVED,
        'published_at' => now()->subDay(),
    ]);

    $this->get("/posts/{$published->slug}")
        ->assertOk()
        ->assertSee('Artikel Publik')
        ->assertSee('Artikel terkait');

    $this->get("/posts/{$draft->slug}")
        ->assertNotFound()
        ->assertSee('Halaman tidak ditemukan');

    $this->get("/posts/{$archived->slug}")
        ->assertNotFound()
        ->assertSee('Halaman tidak ditemukan');
});

test('category dan tag page hanya menampilkan artikel published', function () {
    $published = makePost([
        'title' => 'Cerita Kategori',
        'slug' => 'cerita-kategori',
        'category_id' => $this->categoryCoffee->id,
        'published_at' => now(),
    ]);

    $draft = makePost([
        'title' => 'Cerita Draft',
        'slug' => 'cerita-draft',
        'category_id' => $this->categoryCoffee->id,
        'status' => Post::STATUS_DRAFT,
        'published_at' => null,
    ]);

    $published->tags()->sync([$this->tagLaravel->id]);
    $draft->tags()->sync([$this->tagLaravel->id]);

    $this->get('/category/'.$this->categoryCoffee->slug)
        ->assertOk()
        ->assertSee('Cerita Kategori')
        ->assertDontSee('Cerita Draft');

    $this->get('/tag/'.$this->tagLaravel->slug)
        ->assertOk()
        ->assertSee('Cerita Kategori')
        ->assertDontSee('Cerita Draft');
});

test('search hanya menampilkan artikel published dan query kosong aman', function () {
    makePost([
        'title' => 'Brew Laravel',
        'slug' => 'brew-laravel',
        'category_id' => $this->categoryTech->id,
        'content' => '# Brew Laravel',
        'rendered_content' => '<h1>Brew Laravel</h1>',
        'published_at' => now(),
    ]);

    makePost([
        'title' => 'Brew Draft',
        'slug' => 'brew-draft',
        'category_id' => $this->categoryTech->id,
        'status' => Post::STATUS_DRAFT,
        'published_at' => null,
    ]);

    $this->get('/search?q=brew')
        ->assertOk()
        ->assertSee('Brew Laravel')
        ->assertDontSee('Brew Draft');

    $this->get('/search')
        ->assertOk()
        ->assertSee('Cari artikel');
});

test('halaman tidak ditemukan tampil dengan brand ngopi dulur', function () {
    $this->get('/halaman-yang-tidak-ada')
        ->assertNotFound()
        ->assertSee('Halaman tidak ditemukan')
        ->assertSee('Ngopi dulu, ya.');
});

test('robots txt tersedia dan memblokir route admin serta preview', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin/')
        ->assertSee('Disallow: /*preview*');
});

test('tema public default mengikuti setting', function () {
    SiteSetting::updateOrCreate([
        'key' => 'default_theme',
    ], [
        'value' => 'dark',
        'is_public' => true,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('data-theme="dark"', false);
});
