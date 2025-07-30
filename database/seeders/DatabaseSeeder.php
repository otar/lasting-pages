<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSnapshot;
use App\Models\User;
use App\Observers\PageObserver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Temporarily disable PageObserver to prevent job dispatch during seeding
        Page::unsetEventDispatcher();

        $users = User::factory(count: 10)->create();

        foreach ($users as $user) {
            $pages = Page::factory(count: 100)->create(['user_id' => $user->id]);

            foreach ($pages as $page) {
                // Create mock snapshot data
                $timestamp = now()->format('Y-m-d_H-i-s');
                $baseFileName = Str::slug($page->url).'_'.$timestamp;
                $version = 1;

                $htmlPath = "page-snapshots/{$page->id}/html/{$baseFileName}_v{$version}.html";
                $screenshotPath = "page-snapshots/{$page->id}/screenshots/{$baseFileName}_v{$version}.png";

                // Create mock files
                Storage::put($htmlPath, '<html><body><h1>Mock HTML for '.$page->title.'</h1></body></html>');
                Storage::put($screenshotPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));

                // Create snapshot record
                $pageSnapshot = PageSnapshot::create([
                    'page_id' => $page->id,
                    'html_path' => $htmlPath,
                    'screenshot_path' => $screenshotPath,
                    'version' => $version,
                ]);

                // Update page with current snapshot information
                $page->update([
                    'is_pending' => false,
                    'current_snapshot_id' => $pageSnapshot->id,
                    'current_snapshot_version' => $version,
                ]);
            }
        }
    }
}
