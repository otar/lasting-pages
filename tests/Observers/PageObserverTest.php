<?php

use App\Jobs\ProcessNewPage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('page observer dispatches job when page is created', function () {
    /** @var \Tests\TestCase $this */
    Queue::fake();

    $user = User::factory()->create();

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

test('page observer does not dispatch job when page is updated', function () {
    /** @var \Tests\TestCase $this */
    Queue::fake();

    $user = User::factory()->create();
    $page = Page::factory()->create([
        'user_id' => $user->id,
    ]);

    // Clear any jobs from creation
    Queue::fake();

    $page->update(['title' => 'Updated Title']);

    Queue::assertNotPushed(ProcessNewPage::class);
});

test('page observer does not dispatch job when page is deleted', function () {
    /** @var \Tests\TestCase $this */
    Queue::fake();

    $user = User::factory()->create();
    $page = Page::factory()->create([
        'user_id' => $user->id,
    ]);

    // Clear any jobs from creation
    Queue::fake();

    $page->delete();

    Queue::assertNotPushed(ProcessNewPage::class);
});
