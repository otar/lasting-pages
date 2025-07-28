<?php

use App\Models\Page;
use App\Models\User;
use App\Services\PageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

describe('Page Service', function () {
    test('creates page with valid data', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        Auth::login($user);

        $service = new PageService;
        $page = $service->createPage(
            url: 'https://example.com',
            title: 'Example Title'
        );

        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals($user->id, $page->user_id);
        $this->assertEquals('https://example.com', $page->url);
        $this->assertEquals('Example Title', $page->title);
        $this->assertTrue($page->is_pending);
    });

    test('creates page without title', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        Auth::login($user);

        $service = new PageService;
        $page = $service->createPage(
            url: 'https://example.com'
        );

        $this->assertInstanceOf(Page::class, $page);
        $this->assertNull($page->title);
    });

    test('normalizes url by adding https', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        Auth::login($user);

        $service = new PageService;
        $page = $service->createPage(
            url: 'example.com',
            title: 'Example'
        );

        $this->assertEquals('https://example.com', $page->url);
    });

    test('normalizes url by removing trailing slash', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        Auth::login($user);

        $service = new PageService;
        $page = $service->createPage(
            url: 'https://example.com/',
            title: 'Example'
        );

        $this->assertEquals('https://example.com', $page->url);
    });

    test('handles edge case url normalization', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        Auth::login($user);

        $service = new PageService;
        $page = $service->createPage(
            url: '  https://example.com/path  ',
            title: 'Example'
        );

        $this->assertEquals('https://example.com/path', $page->url);
    });

    test('throws exception when parse_url returns false', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        Auth::login($user);

        $service = new PageService;

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The URL format is invalid.');

        // This URL will cause parse_url to return false
        $service->createPage(
            url: 'http://',
            title: 'Example'
        );
    });

    test('throws exception for duplicate url', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        Auth::login($user);

        // Create first page
        Page::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);

        $service = new PageService;

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('You have already saved this URL.');

        $service->createPage(
            url: 'https://example.com',
            title: 'Duplicate'
        );
    });

    test('allows same url for different users', function () {
        /** @var \Tests\TestCase $this */
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create page for user1
        Page::factory()->create([
            'user_id' => $user1->id,
            'url' => 'https://example.com',
        ]);

        // Should allow user2 to create same url
        Auth::login($user2);
        $service = new PageService;
        $page = $service->createPage(
            url: 'https://example.com',
            title: 'Same URL'
        );

        $this->assertEquals($user2->id, $page->user_id);
        $this->assertEquals('https://example.com', $page->url);
    });

    test('deletes page successfully', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $service = new PageService;
        $result = $service->deletePage($page);

        $this->assertTrue($result);
        $this->assertSoftDeleted('pages', ['id' => $page->id]);
    });

    test('gets user pages in correct order', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        // Create pages with different timestamps
        $oldPage = Page::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
        ]);
        $newPage = Page::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        $service = new PageService;
        $pages = $service->getUserPages($user->id);

        $this->assertCount(2, $pages);

        $firstPage = $pages->first();
        $lastPage = $pages->last();

        $this->assertNotNull($firstPage);
        $this->assertNotNull($lastPage);

        $this->assertEquals($newPage->id, $firstPage->id);
        $this->assertEquals($oldPage->id, $lastPage->id);
    });

    test('gets user pages only for specific user', function () {
        /** @var \Tests\TestCase $this */
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Page::factory()->create(['user_id' => $user1->id]);
        Page::factory()->create(['user_id' => $user1->id]);
        Page::factory()->create(['user_id' => $user2->id]);

        $service = new PageService;
        $user1Pages = $service->getUserPages($user1->id);
        $user2Pages = $service->getUserPages($user2->id);

        $this->assertCount(2, $user1Pages);
        $this->assertCount(1, $user2Pages);
    });
});
