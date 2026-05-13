<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\View\View;

class PublicAboutController extends Controller
{
    public function index(): View
    {
        $setting = SiteSetting::where('key', 'resume_data')->first();
        $resume = $setting ? json_decode($setting->value, true) : null;

        // Fallback defaults jika belum ada data di database
        if (! $resume) {
            $resume = [
                'name' => 'Rifky Awalul Huda',
                'title' => 'IT Support Engineer',
                'location' => 'Karawang, Indonesia',
                'email' => 'rifkyawalhuda@gmail.com',
                'summary' => 'Berpengalaman menangani IT support, mengelola proyek IT, dan mendukung berbagai departemen engineering dengan tools dan aplikasi IT.',
                'experience' => [['position' => 'IT Tech Support', 'company' => 'PT. Sankyu Indonesia International', 'location' => 'Cikarang', 'period' => 'Agustus 2015 — Sekarang', 'items' => ['Meningkatkan sistem dan infrastruktur teknologi informasi secara berkelanjutan.', 'Memberikan dukungan teknis dan troubleshooting untuk masalah hardware, software, dan jaringan di berbagai departemen.', 'Mengelola proyek IT dan mendukung tim engineering dengan tools dan aplikasi IT.']]],
                'projects' => [['name' => 'Employee Hub App', 'period' => 'Jan — Mar 2026', 'description' => 'Sistem manajemen karyawan meliputi pengajuan cuti, surat peringatan, konseling, dan workflow terkait.', 'tech' => 'Node.js · React · Vite · PostgreSQL'], ['name' => 'Computer Inventory App', 'period' => 'Okt — Des 2025', 'description' => 'Sistem manajemen perangkat komputer yang dapat diakses oleh personel yang ditunjuk di seluruh site PT. Sankyu.', 'tech' => 'Node.js · Vue · Bootstrap · PostgreSQL'], ['name' => 'Warehouse Queue App', 'period' => 'Jul — Sep 2025', 'description' => 'Sistem manajemen antrian truk untuk operasi loading dan unloading di warehouse PT. Sankyu.', 'tech' => 'Node.js · Vue · PostgreSQL'], ['name' => 'Transport App', 'period' => 'Jan — Apr 2024', 'description' => 'Aplikasi untuk manajemen penjualan, biaya, transportasi, dan kendaraan.', 'tech' => 'Node.js · Vue · Vite · MySQL · MongoDB']],
                'skills' => ['IT Support', 'Computer Networking', 'Web App Development', 'WordPress CMS', 'Microsoft Office'],
                'education' => ['degree' => 'Sarjana (S1)', 'institution' => 'STMIK Dharma Negara Bandung', 'period' => '2017 — 2021', 'gpa' => '3.33'],
                'certifications' => ['IT Support Fundamental — Google', 'The Complete Cyber Security Course: Network Security — Udemy', 'Build a Full Website Using WordPress — Coursera'],
            ];
        }

        return view('public.about', compact('resume'));
    }
}
