<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    User::updateOrCreate([
        'email' => 'admin@ngopidulur.test',
    ], [
        'name' => 'Admin Ngopi Dulur',
        'password' => Hash::make('password'),
    ]);
});

test('halaman login admin dapat dibuka', function () {
    $this->get('/admin/login')->assertOk();
});

test('login admin berhasil dengan kredensial valid', function () {
    $this->post('/admin/api/login', [
        'email' => 'admin@ngopidulur.test',
        'password' => 'password',
    ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticated();
});

test('login admin menampilkan pesan bahasa indonesia saat kredensial salah', function () {
    $this->from('/admin/login')
        ->post('/admin/api/login', [
            'email' => 'admin@ngopidulur.test',
            'password' => 'salah',
        ])
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors(['email' => 'Email atau kata sandi salah.']);

    $this->assertGuest();
});

test('dashboard admin terlindungi dan logout menghapus session', function () {
    $user = User::where('email', 'admin@ngopidulur.test')->firstOrFail();

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertOk();

    $this->actingAs($user)
        ->post('/admin/api/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
