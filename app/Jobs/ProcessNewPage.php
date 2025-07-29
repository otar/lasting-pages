<?php

namespace App\Jobs;

use App\Models\Page;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNewPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Page $page
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // TODO: Add your page processing logic here
        // For example:
        // - Fetch page metadata
        // - Generate screenshot
        // - Extract content
        // - Update page with fetched data

        // Example: Update the page to mark it as processed
        $this->page->update(['is_pending' => false]);
    }
}
