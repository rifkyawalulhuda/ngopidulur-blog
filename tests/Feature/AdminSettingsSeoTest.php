<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\SiteSetting;
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
        'name' => 'Teknologi',
        'slug' => 'teknologi',
        'description' => 'Catatan teknis.',
        'is_active' => true,
    ]);

    $this->inactiveCategory = Category::create([
        'name' => 'Arsip',
        'slug' => 'arsip',
        'description' => null,
        'is_active' => false,
    ]);

    $this->tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $this->draftOnlyTag = Tag::create([
        'name' => 'Draft',
        'slug' => 'draft',
    ]);
});

function makeSeoPost(User $user, Category $category, array $overrides = []): Post
{
    return Post::create(array_merge([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Artikel Ngopi',
        'slug' => 'artikel-ngopi-'.str()->random(6),
        'excerpt' => 'Ringkasan artikel Ngopi Dulur.',
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

test('admin dapat memperbarui settings beserta aset brand', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->admin)
        ->post('/admin/api/settings', [
            '_method' => 'PUT',
            'site_name' => 'Ngopi Dulur Malam',
            'site_tagline' => 'Hangat, pelan, dan modern',
            'site_description' => 'Ruang baca yang akrab.',
            'logo' => UploadedFile::fake()->image('logo.png', 320, 120)->size(300),
            'favicon' => UploadedFile::fake()->image('favicon.png', 64, 64)->size(120),
            'default_meta_title' => 'Ngopi Dulur Malam',
            'default_meta_description' => 'Deskripsi SEO default yang baru.',
            'default_og_image' => UploadedFile::fake()->image('og.png', 1200, 630)->size(400),
            'footer_note' => 'Footer baru yang lebih hangat.',
            'social_links' => [
                'instagram' => 'https://instagram.com/ngopidulur',
                'github' => 'https://github.com/ngopidulur',
            ],
            'hero_badge' => 'Ngopi Malam',
            'hero_heading' => 'Tempat cerita pulang.',
            'hero_subheading' => 'Tulisan hangat untuk dibaca pelan-pelan.',
            'hero_cta_text' => 'Mulai baca',
            'default_theme' => 'dark_espresso',
        ])
        ->assertOk();

    expect($response->json('item.logo'))->toEndWith('.webp');
    expect($response->json('item.default_og_image'))->toEndWith('.webp');
    expect($response->json('item.favicon'))->toEndWith('.png');

    Storage::disk('public')->assertExists($response->json('item.logo'));
    Storage::disk('public')->assertExists($response->json('item.favicon'));
    Storage::disk('public')->assertExists($response->json('item.default_og_image'));

    $this->actingAs($this->admin)
        ->get('/admin/settings')
        ->assertOk()
        ->assertSee('noindex,nofollow', false);

    $this->get('/')
        ->assertOk()
        ->assertSee('Ngopi Dulur Malam')
        ->assertSee('Footer baru yang lebih hangat.')
        ->assertSee('data-theme="dark"', false);
});

test('media api menampilkan featured image dari posts saja', function () {
    Storage::fake('public');

    Storage::disk('public')->put('posts/cover-one.webp', 'fake-image');

    $withImage = makeSeoPost($this->admin, $this->category, [
        'title' => 'Artikel Dengan Gambar',
        'slug' => 'artikel-dengan-gambar',
        'featured_image' => 'posts/cover-one.webp',
        'featured_image_alt' => 'Sampul satu',
    ]);

    makeSeoPost($this->admin, $this->category, [
        'title' => 'Artikel Tanpa Gambar',
        'slug' => 'artikel-tanpa-gambar',
        'featured_image' => null,
    ]);

    $this->actingAs($this->admin)
        ->getJson('/admin/api/media')
        ->assertOk()
        ->assertJsonCount(1, 'items')
        ->assertJsonPath('items.0.post_title', $withImage->title)
        ->assertJsonPath('items.0.featured_image', 'posts/cover-one.webp');
});

test('sitemap hanya memuat homepage dan konten published yang valid', function () {
    $published = makeSeoPost($this->admin, $this->category, [
        'title' => 'Artikel Publish',
        'slug' => 'artikel-publish',
    ]);

    $draft = makeSeoPost($this->admin, $this->category, [
        'title' => 'Artikel Draft',
        'slug' => 'artikel-draft',
        'status' => Post::STATUS_DRAFT,
        'published_at' => null,
    ]);

    $archived = makeSeoPost($this->admin, $this->category, [
        'title' => 'Artikel Arsip',
        'slug' => 'artikel-arsip',
        'status' => Post::STATUS_ARCHIVED,
    ]);

    $published->tags()->sync([$this->tag->id]);
    $draft->tags()->sync([$this->draftOnlyTag->id]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee(url('/'), false)
        ->assertSee(route('posts.show', $published->slug), false)
        ->assertSee(route('category.show', $this->category->slug), false)
        ->assertSee(route('tag.show', $this->tag->slug), false)
        ->assertDontSee(route('posts.show', $draft->slug), false)
        ->assertDontSee(route('posts.show', $archived->slug), false)
        ->assertDontSee(route('category.show', $this->inactiveCategory->slug), false)
        ->assertDontSee(route('tag.show', $this->draftOnlyTag->slug), false)
        ->assertDontSee('/search', false)
        ->assertDontSee('/admin', false);
});

test('robots txt menyertakan sitemap dan search page noindex memakai fallback seo', function () {
    SiteSetting::updateOrCreate(['key' => 'default_meta_title'], ['value' => 'Ngopi Dulur SEO', 'is_public' => true]);
    SiteSetting::updateOrCreate(['key' => 'default_meta_description'], ['value' => 'Deskripsi SEO default.', 'is_public' => true]);
    SiteSetting::updateOrCreate(['key' => 'hero_cta_text'], ['value' => 'Cari sekarang', 'is_public' => true]);

    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: '.route('sitemap'));

    $this->get('/')
        ->assertOk()
        ->assertSee('<title>Ngopi Dulur SEO</title>', false)
        ->assertSee('<meta name="description" content="Deskripsi SEO default.">', false)
        ->assertSee('Cari sekarang');

    $this->get('/search?q=laravel')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false);
});
