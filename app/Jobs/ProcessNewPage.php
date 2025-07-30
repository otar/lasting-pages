<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\PageSnapshot;
use App\Services\BrowserRenderingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessNewPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Page $page,
    ) {}

    public function handle(BrowserRenderingService $browserRenderingService): void
    {
        $snapshot = $browserRenderingService->snapshot(
            websiteUrl: $this->page->url
        );

        $newVersion = ($this->page->current_snapshot_version ?? 0) + 1;

        $timestamp = now()->format('Y-m-d_H-i-s');
        $baseFileName = Str::slug($this->page->url).'_'.$timestamp;

        $htmlPath = "page-snapshots/{$this->page->id}/html/{$baseFileName}_v{$newVersion}.html";
        Storage::put($htmlPath, $snapshot->html);

        $screenshotData = base64_decode($snapshot->screenshot);
        $screenshotPath = "page-snapshots/{$this->page->id}/screenshots/{$baseFileName}_v{$newVersion}.png";
        Storage::put($screenshotPath, $screenshotData);

        $pageSnapshot = PageSnapshot::create([
            'page_id' => $this->page->id,
            'html_path' => $htmlPath,
            'screenshot_path' => $screenshotPath,
            'version' => $newVersion,
        ]);

        $this->page->update([
            'is_pending' => false,
            'current_snapshot_id' => $pageSnapshot->id,
            'current_snapshot_version' => $newVersion,
        ]);
    }
}
