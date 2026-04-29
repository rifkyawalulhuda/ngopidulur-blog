<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class PublicRobotsController extends Controller
{
    public function show(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Disallow: /admin/',
            'Disallow: /admin/api/',
            'Disallow: /admin/login',
            'Disallow: /*preview*',
            '',
        ]);

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
