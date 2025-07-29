<?php

namespace App\Http\Controllers;

use App\Services\PageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController
{
    public function __construct(
        private PageService $pageService
    ) {}

    public function index(Request $request): View
    {
        $sortOrder = $request->string('sort', 'desc')->toString();
        $pages = $this->pageService->getUserPages((int) auth()->id(), $sortOrder);

        return view('dashboard', compact('pages', 'sortOrder'));
    }
}
