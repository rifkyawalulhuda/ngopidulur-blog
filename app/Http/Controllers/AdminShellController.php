<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminShellController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
        ]);
    }
}
