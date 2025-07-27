<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('login page displays correctly', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertSee('Login');
    $response->assertSee('Email');
    $response->assertSee('Password');
});

test('register page displays correctly', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertSee('Register');
    $response->assertSee('Email');
    $response->assertSee('Password');
    $response->assertSee('Confirm Password');
});

test('user can register with valid data', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->post(route('register'), [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');

    expect(\App\Models\User::where('email', 'test@example.com')->exists())->toBeTrue();

    $this->assertAuthenticated();
});

test('user cannot register with invalid email', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->post(route('register'), [
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user cannot register with short password', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->post(route('register'), [
        'email' => 'test@example.com',
        'password' => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('user cannot register with mismatched passwords', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->post(route('register'), [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('user cannot register with duplicate email', function () {
    /** @var \Tests\TestCase $this */
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->post(route('register'), [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user can login with valid credentials', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with invalid credentials', function () {
    /** @var \Tests\TestCase $this */
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('user cannot login with non-existent email', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->post(route('login'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('authenticated user can logout', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect('/');
    $this->assertGuest();
});

test('guest can access logout endpoint', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->post(route('logout'));

    $response->assertRedirect('/');
});
