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
    $response->assertSee($user->name);
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
    $response->assertSee('Save a URL to read later');
    $response->assertSee('Save Page');
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
