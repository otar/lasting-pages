<?php

use App\Jobs\ProcessNewPage;
use App\Models\Page;
use App\Models\User;
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

    $job = new ProcessNewPage($page);
    $job->handle();

    $page->refresh();
    expect($page->is_pending)->toBeFalse();
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
