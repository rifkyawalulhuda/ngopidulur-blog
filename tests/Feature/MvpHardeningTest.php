<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostPublishingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@ngopidulur.test',
        'name' => 'Admin Ngopi Dulur',
        'role' => 'admin',
        'status' => 'active',
    ]);

    $this->category = Category::create([
        'name' => 'Ngopi & Santai',
        'slug' => 'ngopi-santai',
        'description' => 'Catatan hangat.',
        'is_active' => true,
    ]);

    $this->tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
});

function makeHardeningPost(User $user, Category $category, array $overrides = []): Post
{
    return Post::create(array_merge([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Artikel Hangat',
        'slug' => 'artikel-hangat-'.str()->random(6),
        'excerpt' => 'Ringkasan artikel hangat.',
        'content_format' => Post::CONTENT_FORMAT_MARKDOWN,
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

test('guest diarahkan ke login saat mengakses route admin yang terlindungi', function (string $path) {
    $this->get($path)
        ->assertRedirect('/admin/login');
})->with([
    '/admin/dashboard',
    '/admin/api/posts',
    '/admin/api/settings',
    '/admin/api/media',
]);

test('surface publik hanya menampilkan artikel published dan menyembunyikan draft serta archived di semua surface utama', function () {
    $main = makeHardeningPost($this->admin, $this->category, [
        'title' => 'Artikel Publik Utama',
        'slug' => 'artikel-publik-utama',
        'is_featured' => true,
        'published_at' => now()->subHour(),
    ]);

    $relatedPublished = makeHardeningPost($this->admin, $this->category, [
        'title' => 'Artikel Related Published',
        'slug' => 'artikel-related-published',
        'published_at' => now()->subMinutes(30),
    ]);

    $draft = makeHardeningPost($this->admin, $this->category, [
        'title' => 'Artikel Draft Rahasia',
        'slug' => 'artikel-draft-rahasia',
        'status' => Post::STATUS_DRAFT,
        'published_at' => null,
    ]);

    $archived = makeHardeningPost($this->admin, $this->category, [
        'title' => 'Artikel Arsip Senyap',
        'slug' => 'artikel-arsip-senyap',
        'status' => Post::STATUS_ARCHIVED,
    ]);

    $main->tags()->sync([$this->tag->id]);
    $relatedPublished->tags()->sync([$this->tag->id]);
    $draft->tags()->sync([$this->tag->id]);
    $archived->tags()->sync([$this->tag->id]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Artikel Publik Utama')
        ->assertSee('Artikel Related Published')
        ->assertDontSee('Artikel Draft Rahasia')
        ->assertDontSee('Artikel Arsip Senyap');

    $this->get('/posts/'.$main->slug)
        ->assertOk()
        ->assertSee('Artikel Related Published')
        ->assertDontSee('Artikel Draft Rahasia')
        ->assertDontSee('Artikel Arsip Senyap');

    $this->get('/category/'.$this->category->slug)
        ->assertOk()
        ->assertSee('Artikel Publik Utama')
        ->assertSee('Artikel Related Published')
        ->assertDontSee('Artikel Draft Rahasia')
        ->assertDontSee('Artikel Arsip Senyap');

    $this->get('/tag/'.$this->tag->slug)
        ->assertOk()
        ->assertSee('Artikel Publik Utama')
        ->assertSee('Artikel Related Published')
        ->assertDontSee('Artikel Draft Rahasia')
        ->assertDontSee('Artikel Arsip Senyap');

    $this->get('/search?q=Artikel')
        ->assertOk()
        ->assertSee('Artikel Publik Utama')
        ->assertSee('Artikel Related Published')
        ->assertDontSee('Artikel Draft Rahasia')
        ->assertDontSee('Artikel Arsip Senyap');

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee(route('posts.show', $main->slug), false)
        ->assertSee(route('posts.show', $relatedPublished->slug), false)
        ->assertDontSee(route('posts.show', $draft->slug), false)
        ->assertDontSee(route('posts.show', $archived->slug), false);
});

test('validasi artikel mewajibkan kategori dan content format yang valid', function (array $payload, string $field, ?string $message = null) {
    $response = $this->actingAs($this->admin)
        ->postJson('/admin/api/posts', array_merge([
            'title' => 'Artikel Validasi',
            'slug' => '',
            'excerpt' => null,
            'content_format' => Post::CONTENT_FORMAT_MARKDOWN,
            'content' => '# Halo',
            'category_id' => $this->category->id,
            'status' => Post::STATUS_DRAFT,
        ], $payload))
        ->assertStatus(422)
        ->assertJsonValidationErrors([$field]);

    if ($message !== null) {
        $response->assertJsonPath("errors.{$field}.0", $message);
    }
})->with([
    'kategori wajib' => [
        ['category_id' => null],
        'category_id',
        'Kategori wajib dipilih.',
    ],
    'format konten wajib' => [
        ['content_format' => null],
        'content_format',
        'Format konten wajib dipilih.',
    ],
    'format konten invalid' => [
        ['content_format' => 'html'],
        'content_format',
        'Format konten harus Visual atau Markdown.',
    ],
]);

test('published_at otomatis diisi saat publish jika kosong', function () {
    Storage::fake('public');

    $created = $this->actingAs($this->admin)
        ->post('/admin/api/posts', [
            'title' => 'Artikel Auto Publish',
            'slug' => '',
            'excerpt' => null,
            'content_format' => Post::CONTENT_FORMAT_MARKDOWN,
            'content' => '# Halo',
            'category_id' => $this->category->id,
            'status' => Post::STATUS_DRAFT,
            'featured_image' => UploadedFile::fake()->image('cover.png', 1200, 800)->size(1200),
        ])
        ->assertCreated()
        ->json('item');

    $this->actingAs($this->admin)
        ->postJson("/admin/api/posts/{$created['id']}/publish")
        ->assertOk()
        ->assertJsonPath('item.status', Post::STATUS_PUBLISHED);

    expect(Post::findOrFail($created['id'])->published_at)->not->toBeNull();
});

test('upload artikel menolak file non gambar dan file yang terlalu besar', function (UploadedFile $file, string $expectedMessage) {
    $this->actingAs($this->admin)
        ->withHeaders([
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->post('/admin/api/posts', [
            'title' => 'Artikel Upload',
            'slug' => '',
            'excerpt' => null,
            'content_format' => Post::CONTENT_FORMAT_RICHTEXT,
            'content' => '<p>Konten</p>',
            'category_id' => $this->category->id,
            'status' => Post::STATUS_DRAFT,
            'featured_image' => $file,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['featured_image'])
        ->assertJsonPath('errors.featured_image.0', $expectedMessage);
})->with([
    'non image' => [
        UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        'File unggulan harus berupa gambar.',
    ],
    'oversized image' => [
        UploadedFile::fake()->image('large.png', 4000, 3000)->size(3000),
        'Ukuran gambar maksimal 2 MB.',
    ],
]);

test('gagal konversi webp dikembalikan sebagai error yang aman', function () {
    $this->mock(PostPublishingService::class, function (MockInterface $mock): void {
        $mock->shouldReceive('renderContent')->andReturn('<p>Konten aman</p>');
        $mock->shouldReceive('estimateReadingTime')->andReturn(1);
        $mock->shouldReceive('storeFeaturedImage')->andThrow(new RuntimeException('Gagal mengonversi gambar ke WebP.'));
    });

    $this->actingAs($this->admin)
        ->post('/admin/api/posts', [
            'title' => 'Artikel Gagal WebP',
            'slug' => '',
            'excerpt' => null,
            'content_format' => Post::CONTENT_FORMAT_RICHTEXT,
            'content' => '<p>Konten</p>',
            'category_id' => $this->category->id,
            'status' => Post::STATUS_DRAFT,
            'featured_image' => UploadedFile::fake()->image('cover.png', 1200, 800)->size(1200),
        ])
        ->assertStatus(422)
        ->assertJsonPath('message', 'Gagal mengonversi gambar ke WebP.');
});

test('post tetap valid setelah tag dihapus dan pagination tetap diterapkan', function () {
    $post = makeHardeningPost($this->admin, $this->category, [
        'title' => 'Artikel Bertag',
        'slug' => 'artikel-bertag',
    ]);

    $post->tags()->sync([$this->tag->id]);

    $this->actingAs($this->admin)
        ->deleteJson('/admin/api/tags/'.$this->tag->slug)
        ->assertOk();

    $this->get('/posts/'.$post->slug)
        ->assertOk()
        ->assertSee('Artikel Bertag');

    foreach (range(1, 10) as $index) {
        makeHardeningPost($this->admin, $this->category, [
            'title' => 'Pagination Post '.$index,
            'slug' => 'pagination-post-'.$index,
            'published_at' => now()->subMinutes($index + 5),
        ]);
    }

    $this->actingAs($this->admin)
        ->getJson('/admin/api/posts')
        ->assertOk()
        ->assertJsonPath('meta.per_page', 10)
        ->assertJsonCount(10, 'items');

    $this->get('/category/'.$this->category->slug)
        ->assertOk()
        ->assertSee('?page=2', false);
});

test('model utama memakai mass assignment yang eksplisit', function (string $class) {
    expect((new $class())->getFillable())->not->toBeEmpty();
})->with([
    \App\Models\User::class,
    \App\Models\Post::class,
    \App\Models\Category::class,
    \App\Models\Tag::class,
    \App\Models\SiteSetting::class,
]);
