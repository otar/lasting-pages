<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('forgot password page displays correctly', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
    $response->assertSee('Reset Password');
    $response->assertSee('Email Address');
    $response->assertSee('Send Reset Link');
});

test('user can request password reset with valid email', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create(['email' => 'test@example.com']);

    $response = $this->post('/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');
});

test('user cannot request password reset with invalid email', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->post('/forgot-password', [
        'email' => 'invalid-email',
    ]);

    $response->assertSessionHasErrors('email');
});

test('user cannot request password reset with non-existent email', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->post('/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('email');
});

test('reset password page displays correctly with valid token', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    $response = $this->get("/reset-password/{$token}?email=test@example.com");

    $response->assertStatus(200);
    $response->assertSee('Reset Password');
    $response->assertSee('New Password');
    $response->assertSee('Confirm New Password');
});

test('user can reset password with valid token and data', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHas('status');

    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

test('user cannot reset password with invalid token', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create(['email' => 'test@example.com']);

    $response = $this->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('email');
});

test('user cannot reset password with invalid email', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'invalid-email',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors('email');
});

test('user cannot reset password with short password', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
});

test('user cannot reset password with mismatched passwords', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'different123',
    ]);

    $response->assertSessionHasErrors('password');
});

test('login form has forgot password link', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Forgot your password?');
});

test('password reset token expires and cannot be reused', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    // Use the token once
    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    // Try to use the same token again
    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'anotherpassword123',
        'password_confirmation' => 'anotherpassword123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('email');
});
