<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can access dashboard', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee($user->name);
});

test('guest cannot access dashboard', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('dashboard shows user information', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertSee('john@example.com');
});

test('dashboard has logout functionality', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Logout');
});
