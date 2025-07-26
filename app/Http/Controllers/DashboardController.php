<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController
{
    public function index(): View
    {
        return view('dashboard');
    }
}
