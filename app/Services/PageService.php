<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Pagination\LengthAwarePaginator as ConcretePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PageService
{
    public function createPage(string $url, ?string $title = null): Page
    {
        $normalizedUrl = $this->normalizeUrl($url);

        $this->validateUniqueUrl($normalizedUrl, (int) Auth::id());

        return Page::create([
            'user_id' => Auth::id(),
            'url' => $normalizedUrl,
            'title' => $title,
            'is_pending' => true,
        ]);
    }

    public function deletePage(Page $page): ?bool
    {
        return $page->delete();
    }

    /**
     * @return ConcretePaginator<int, Page>
     */
    public function getUserPages(int $userId, string $sortOrder = 'desc', int $perPage = 25): ConcretePaginator
    {
        // Validate sort order
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

        // Validate per page
        $perPage = in_array($perPage, [10, 25, 50]) ? $perPage : 25;

        return Page::where('user_id', $userId)
            ->orderBy('created_at', $sortOrder)
            ->paginate($perPage)
            ->withQueryString();
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);

        if (! preg_match('/^https?:\/\//', $url)) {
            $url = 'https://'.$url;
        }

        $url = rtrim($url, '/');

        $parsedUrl = parse_url($url);
        if (! $parsedUrl || ! isset($parsedUrl['host'])) {
            throw ValidationException::withMessages([
                'url' => 'The URL format is invalid.',
            ]);
        }

        return $url;
    }

    private function validateUniqueUrl(string $url, int $userId): void
    {
        $exists = Page::where('user_id', $userId)
            ->where('url', $url)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'url' => 'You have already saved this URL.',
            ]);
        }
    }
}
