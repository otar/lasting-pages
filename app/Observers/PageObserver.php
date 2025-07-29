<?php

namespace App\Observers;

use App\Jobs\ProcessNewPage;
use App\Models\Page;

class PageObserver
{
    public function created(Page $page): void
    {
        ProcessNewPage::dispatch($page);
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function updated(Page $page): void
    {
        // No action needed for page updates
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function deleted(Page $page): void
    {
        // No action needed for page deletion
    }
}
