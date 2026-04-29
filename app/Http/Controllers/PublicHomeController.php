<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicHomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'title' => 'Ngopi Dulur',
        ]);
    }
}
