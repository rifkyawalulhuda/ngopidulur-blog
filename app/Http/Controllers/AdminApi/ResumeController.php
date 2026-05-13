<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    private const KEY = 'resume_data';

    private const DEFAULTS = [
        'name' => 'Rifky Awalul Huda',
        'title' => 'IT Support Engineer',
        'location' => 'Karawang, Indonesia',
        'email' => 'rifkyawalhuda@gmail.com',
        'summary' => 'Berpengalaman menangani IT support, mengelola proyek IT, dan mendukung berbagai departemen engineering dengan tools dan aplikasi IT.',
        'experience' => [
            [
                'position' => 'IT Tech Support',
                'company' => 'PT. Sankyu Indonesia International',
                'location' => 'Cikarang',
                'period' => 'Agustus 2015 — Sekarang',
                'items' => [
                    'Meningkatkan sistem dan infrastruktur teknologi informasi secara berkelanjutan.',
                    'Memberikan dukungan teknis dan troubleshooting untuk masalah hardware, software, dan jaringan di berbagai departemen.',
                    'Mengelola proyek IT dan mendukung tim engineering dengan tools dan aplikasi IT.',
                ],
            ],
        ],
        'projects' => [
            [
                'name' => 'Employee Hub App',
                'period' => 'Jan — Mar 2026',
                'description' => 'Sistem manajemen karyawan meliputi pengajuan cuti, surat peringatan, konseling, dan workflow terkait.',
                'tech' => 'Node.js · React · Vite · PostgreSQL',
            ],
            [
                'name' => 'Computer Inventory App',
                'period' => 'Okt — Des 2025',
                'description' => 'Sistem manajemen perangkat komputer yang dapat diakses oleh personel yang ditunjuk di seluruh site PT. Sankyu.',
                'tech' => 'Node.js · Vue · Bootstrap · PostgreSQL',
            ],
            [
                'name' => 'Warehouse Queue App',
                'period' => 'Jul — Sep 2025',
                'description' => 'Sistem manajemen antrian truk untuk operasi loading dan unloading di warehouse PT. Sankyu.',
                'tech' => 'Node.js · Vue · PostgreSQL',
            ],
            [
                'name' => 'Transport App',
                'period' => 'Jan — Apr 2024',
                'description' => 'Aplikasi untuk manajemen penjualan, biaya, transportasi, dan kendaraan.',
                'tech' => 'Node.js · Vue · Vite · MySQL · MongoDB',
            ],
        ],
        'skills' => ['IT Support', 'Computer Networking', 'Web App Development', 'WordPress CMS', 'Microsoft Office'],
        'education' => [
            'degree' => 'Sarjana (S1)',
            'institution' => 'STMIK Dharma Negara Bandung',
            'period' => '2017 — 2021',
            'gpa' => '3.33',
        ],
        'certifications' => [
            'IT Support Fundamental — Google',
            'The Complete Cyber Security Course: Network Security — Udemy',
            'Build a Full Website Using WordPress — Coursera',
        ],
    ];

    public function show(): JsonResponse
    {
        $setting = SiteSetting::where('key', self::KEY)->first();

        $data = $setting ? json_decode($setting->value, true) : self::DEFAULTS;

        return response()->json(['item' => $data]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'experience' => ['nullable', 'array'],
            'experience.*.position' => ['required', 'string'],
            'experience.*.company' => ['required', 'string'],
            'experience.*.location' => ['nullable', 'string'],
            'experience.*.period' => ['nullable', 'string'],
            'experience.*.items' => ['nullable', 'array'],
            'projects' => ['nullable', 'array'],
            'projects.*.name' => ['required', 'string'],
            'projects.*.period' => ['nullable', 'string'],
            'projects.*.description' => ['nullable', 'string'],
            'projects.*.tech' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'education' => ['nullable', 'array'],
            'certifications' => ['nullable', 'array'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'title.required' => 'Judul/posisi wajib diisi.',
        ]);

        SiteSetting::updateOrCreate(
            ['key' => self::KEY],
            ['value' => json_encode($data, JSON_UNESCAPED_UNICODE), 'is_public' => true]
        );

        return response()->json([
            'message' => 'Resume berhasil diperbarui.',
            'item' => $data,
        ]);
    }
}
