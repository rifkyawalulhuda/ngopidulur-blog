<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'name' => 'Admin Lama',
        'email' => 'admin@ngopidulur.test',
        'password' => Hash::make('password-lama'),
        'role' => 'admin',
        'status' => 'active',
    ]);
});

test('admin dapat melihat profil sendiri', function () {
    $this->actingAs($this->admin)
        ->getJson('/admin/api/profile')
        ->assertOk()
        ->assertJsonPath('item.name', 'Admin Lama')
        ->assertJsonPath('item.email', 'admin@ngopidulur.test')
        ->assertJsonPath('item.role_label', 'Administrator')
        ->assertJsonPath('item.avatar_url', '/images/user/owner.png');
});

test('admin dapat memperbarui nama email password dan avatar', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->admin)
        ->post('/admin/api/profile', [
            'name' => 'Admin Baru',
            'email' => 'admin-baru@ngopidulur.test',
            'current_password' => 'password-lama',
            'password' => 'password-baru-aman',
            'password_confirmation' => 'password-baru-aman',
            'avatar' => UploadedFile::fake()->image('avatar.png', 320, 320)->size(200),
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->assertOk()
        ->assertJsonPath('message', 'Profil berhasil diperbarui.')
        ->assertJsonPath('item.name', 'Admin Baru')
        ->assertJsonPath('item.email', 'admin-baru@ngopidulur.test');

    $this->admin->refresh();

    expect($this->admin->avatar)->toStartWith('avatars/');
    expect(Hash::check('password-baru-aman', $this->admin->password))->toBeTrue();
    expect($response->json('item.avatar_url'))->toStartWith('/storage/avatars/');

    Storage::disk('public')->assertExists($this->admin->avatar);
});

test('password saat ini wajib benar untuk mengganti password', function () {
    $this->actingAs($this->admin)
        ->post('/admin/api/profile', [
            'name' => 'Admin Baru',
            'email' => 'admin@ngopidulur.test',
            'current_password' => 'password-salah',
            'password' => 'password-baru-aman',
            'password_confirmation' => 'password-baru-aman',
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['current_password']);
});
