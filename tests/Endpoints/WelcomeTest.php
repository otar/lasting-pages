<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('welcome page displays correctly for guests', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Welcome');
});

test('welcome page displays correctly for authenticated users', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    $response->assertSee('Welcome');
});

test('welcome page has navigation links for guests', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Login');
    $response->assertSee('Register');
});

test('welcome page has navigation links for authenticated users', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee('Logout');
});
