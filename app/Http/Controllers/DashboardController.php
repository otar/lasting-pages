<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\Contracts\View\View;

class DashboardController
{
    public function __construct(
        private PageService $pageService
    ) {}

    public function index(): View
    {
        $pages = $this->pageService->getUserPages((int) auth()->id());

        return view('dashboard', compact('pages'));
    }
}
