<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\PageService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController
{
    use AuthorizesRequests;

    public function __construct(
        private PageService $pageService
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'url' => 'required|url|max:2000',
            'title' => 'nullable|string|max:250',
        ]);

        try {
            $this->pageService->createPage([
                'url' => $request->string('url')->toString(),
                'title' => $request->string('title', '')->value() ?: null,
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Page saved successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['url' => $e->getMessage()]);
        }
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $this->pageService->deletePage($page);

        return redirect()->route('dashboard')
            ->with('success', 'Page deleted successfully!');
    }
}
