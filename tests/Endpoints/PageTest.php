<?php

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Page Store Endpoint', function () {
    test('authenticated user can create a page with valid data', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        $pageData = [
            'url' => 'https://example.com',
        ];

        $response = $this->actingAs($user)->post(route('pages.store'), $pageData);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Page saved successfully!');

        $this->assertDatabaseHas('pages', [
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'title' => null,
        ]);
    });

    test('authenticated user can create a page without title', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        $pageData = [
            'url' => 'https://example.com',
        ];

        $response = $this->actingAs($user)->post(route('pages.store'), $pageData);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Page saved successfully!');

        $this->assertDatabaseHas('pages', [
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'title' => null,
        ]);
    });

    test('authenticated user can create a page with empty title', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        $pageData = [
            'url' => 'https://example.com',
            'title' => '',
        ];

        $response = $this->actingAs($user)->post(route('pages.store'), $pageData);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Page saved successfully!');

        $this->assertDatabaseHas('pages', [
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'title' => null,
        ]);
    });

    test('page creation fails without url', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        $pageData = [
            'title' => 'Example Title',
        ];

        $response = $this->actingAs($user)->post(route('pages.store'), $pageData);

        $response->assertSessionHasErrors(['url']);
        $this->assertDatabaseMissing('pages', [
            'user_id' => $user->id,
            'title' => 'Example Title',
        ]);
    });

    test('page creation fails with invalid url', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        $pageData = [
            'url' => 'not-a-valid-url',
            'title' => 'Example Title',
        ];

        $response = $this->actingAs($user)->post(route('pages.store'), $pageData);

        $response->assertSessionHasErrors(['url']);
        $this->assertDatabaseMissing('pages', [
            'user_id' => $user->id,
            'url' => 'not-a-valid-url',
        ]);
    });

    test('page creation fails with url too long', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        $pageData = [
            'url' => 'https://example.com/'.str_repeat('a', 2000),
            'title' => 'Example Title',
        ];

        $response = $this->actingAs($user)->post(route('pages.store'), $pageData);

        $response->assertSessionHasErrors(['url']);
    });

    test('guest cannot create a page', function () {
        /** @var \Tests\TestCase $this */
        $pageData = [
            'url' => 'https://example.com',
        ];

        $response = $this->post(route('pages.store'), $pageData);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('pages', [
            'url' => 'https://example.com',
        ]);
    });

    test('page creation handles service exception', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        // Mock the PageService to throw an exception
        $this->mock(\App\Services\PageService::class, function (\Mockery\MockInterface $mock) {
            // @phpstan-ignore method.notFound
            $mock->shouldReceive('createPage')
                ->andReturnUsing(function () {
                    throw new \Exception('Service error');
                });
        });

        $pageData = [
            'url' => 'https://example.com',
        ];

        $response = $this->actingAs($user)->post(route('pages.store'), $pageData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['url' => 'Service error']);
    });
});

describe('Page Destroy Endpoint', function () {
    test('authenticated user can delete their own page', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);
        $page->refresh(); // Refresh to get the encoded_id

        $response = $this->actingAs($user)->delete(route('pages.destroy', $page));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Page deleted successfully!');

        $this->assertSoftDeleted('pages', [
            'id' => $page->id,
        ]);
    });

    test('authenticated user cannot delete another users page', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $otherUser->id]);
        $page->refresh(); // Refresh to get the encoded_id

        $response = $this->actingAs($user)->delete(route('pages.destroy', $page));

        $response->assertStatus(403);
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'deleted_at' => null,
        ]);
    });

    test('guest cannot delete a page', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);
        $page->refresh(); // Refresh to get the encoded_id

        $response = $this->delete(route('pages.destroy', $page));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'deleted_at' => null,
        ]);
    });

    test('deleting non-existent page returns 404', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('pages.destroy', 999));

        $response->assertStatus(404);
    });
});
