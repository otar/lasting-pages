<?php

use App\DataTransferObjects\BrowserRenderingSnapshotDto;
use App\Jobs\ProcessNewPage;
use App\Models\Page;
use App\Models\User;
use App\Services\BrowserRenderingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('process new page job is dispatched when page is created', function () {
    /** @var \Tests\TestCase $this */
    Queue::fake();

    $user = User::factory()->create();
    $this->actingAs($user);

    $page = Page::create([
        'user_id' => $user->id,
        'url' => 'https://example.com',
        'title' => 'Example Page',
        'is_pending' => true,
    ]);

    Queue::assertPushed(ProcessNewPage::class, function (ProcessNewPage $job) use ($page) {
        return $job->page->id === $page->id;
    });
});

test('process new page job updates is_pending to false', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $page = Page::factory()->create([
        'user_id' => $user->id,
        'is_pending' => true,
    ]);

    expect($page->is_pending)->toBeTrue();

    /** @var BrowserRenderingService&\Mockery\MockInterface $mockBrowserService */
    $mockBrowserService = $this->mock(BrowserRenderingService::class, function (\Mockery\MockInterface $mock) {
        /** @phpstan-ignore-next-line */
        $mock->shouldReceive('snapshot')
            ->once()
            ->andReturn(new BrowserRenderingSnapshotDto(
                screenshot: base64_encode('fake-screenshot-data'),
                html: '<html><body>Test</body></html>'
            ));
    });

    $job = new ProcessNewPage($page);
    $job->handle($mockBrowserService);

    $page->refresh();
    expect($page->is_pending)->toBeFalse();
    expect($page->current_snapshot_id)->not->toBeNull();
    expect($page->current_snapshot_version)->toBe(1);
});

test('process new page job can be queued', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $page = Page::factory()->create([
        'user_id' => $user->id,
        'is_pending' => true,
    ]);

    $job = new ProcessNewPage($page);

    expect($job)->toBeInstanceOf(ProcessNewPage::class);
    expect($job->page->id)->toBe($page->id);
});
