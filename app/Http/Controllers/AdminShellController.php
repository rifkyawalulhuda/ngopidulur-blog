<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminShellController extends Controller
{
    public function index(): View
    {
        if (request()->routeIs('admin.index') || request()->routeIs('admin.dashboard')) {
            return view('pages.dashboard.blog-dashboard', [
                'title' => 'Dashboard',
            ]);
        }

        return view('admin.dashboard', [
            'title' => 'Admin',
        ]);
    }
}
