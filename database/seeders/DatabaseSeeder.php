<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@ngopidulur.test',
        ], [
            'name' => 'Admin Ngopi Dulur',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'bio' => 'Penulis utama Ngopi Dulur.',
            'avatar' => null,
        ]);

        $categories = [
            'Catatan Harian',
            'Teknologi',
            'Ngopi & Santai',
        ];

        foreach ($categories as $categoryName) {
            Category::updateOrCreate([
                'slug' => Str::slug($categoryName),
            ], [
                'name' => $categoryName,
                'description' => null,
                'is_active' => true,
            ]);
        }

        $settings = [
            'site_name' => 'Ngopi Dulur',
            'site_tagline' => 'Warm Coffee Meets Modern Tech',
            'site_description' => 'Blog pribadi hangat untuk catatan, ide, dan tulisan santai.',
            'hero_badge' => 'Ngopi Dulur',
            'hero_heading' => 'Cerita, catatan, dan pikiran ringan yang enak dibaca sambil ngopi.',
            'hero_subheading' => 'Seduh bacaan terbaru dari ruang tulis pribadi yang modern dan hangat.',
            'footer_note' => 'Dibuat dengan Laravel, Vue, dan secangkir kopi yang pelan-pelan habis.',
            'default_meta_title' => 'Ngopi Dulur',
            'default_meta_description' => 'Personal blog CMS dengan nuansa hangat dan fondasi modern.',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate([
                'key' => $key,
            ], [
                'value' => $value,
                'is_public' => true,
            ]);
        }
    }
}
