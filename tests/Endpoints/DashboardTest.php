<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can access dashboard', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee($user->email);
});

test('guest cannot access dashboard', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('dashboard shows add page form', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Add New Page');
    $response->assertSee('Save Page');
    $response->assertSee('Enter URL (https://example.com)');
});

test('dashboard shows saved pages section', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('My Saved Pages');
    $response->assertSee('No pages saved yet');
});

test('dashboard has logout functionality', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Logout');
});

test('dashboard shows sorting options when pages exist', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->pages()->createMany([
        ['url' => 'https://example1.com', 'title' => 'Example 1'],
        ['url' => 'https://example2.com', 'title' => 'Example 2'],
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Newest First');
    $response->assertSee('Oldest First');
});

test('dashboard does not show sorting options when no pages exist', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertDontSee('Newest First');
    $response->assertDontSee('Oldest First');
});

test('dashboard applies desc sort by default', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $oldPage = $user->pages()->create(['url' => 'https://old.com', 'title' => 'Old Page', 'created_at' => now()->subDays(2)]);
    $newPage = $user->pages()->create(['url' => 'https://new.com', 'title' => 'New Page', 'created_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSeeInOrder(['New Page', 'Old Page']);
});

test('dashboard applies asc sort when requested', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $oldPage = $user->pages()->create(['url' => 'https://old.com', 'title' => 'Old Page', 'created_at' => now()->subDays(2)]);
    $newPage = $user->pages()->create(['url' => 'https://new.com', 'title' => 'New Page', 'created_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard', ['sort' => 'asc']));

    $response->assertStatus(200);
    $response->assertSeeInOrder(['Old Page', 'New Page']);
});

test('dashboard shows per page options when pages exist', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->pages()->createMany([
        ['url' => 'https://example1.com', 'title' => 'Example 1'],
        ['url' => 'https://example2.com', 'title' => 'Example 2'],
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Show:');
    $response->assertSee('10');
    $response->assertSee('25');
    $response->assertSee('50');
});

test('dashboard respects per page parameter', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    // Create 15 pages
    for ($i = 1; $i <= 15; $i++) {
        $user->pages()->create(['url' => "https://example{$i}.com", 'title' => "Page {$i}"]);
    }

    $response = $this->actingAs($user)->get(route('dashboard', ['per_page' => 10]));

    $response->assertStatus(200);
    // Should see 10 pages (newest first: Page 15 down to Page 6)
    for ($i = 6; $i <= 15; $i++) {
        $response->assertSee("Page {$i}");
    }
    // Check pagination shows correctly (should show page numbers)
    $response->assertSee('Pages:');
    // Should be on page 1
    $response->assertSee('<strong>1</strong>', false);
    // Should see link to page 2
    $response->assertSee('<a href="', false);
    $response->assertSee('class="text-decoration-none">2</a>', false);
});
