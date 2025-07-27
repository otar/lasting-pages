<?php

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('UserService', function () {
    test('gravatarUrl generates correct URL for User model', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'test@example.com']);
        $userService = new UserService;

        $url = $userService->gravatarUrl($user);

        $expectedHash = hash('sha256', 'test@example.com');
        $expectedUrl = "https://gravatar.com/avatar/{$expectedHash}?d=mp";

        expect($url)->toBe($expectedUrl);
    });

    test('gravatarUrl generates correct URL for different email', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'another@example.com']);
        $userService = new UserService;

        $url = $userService->gravatarUrl($user);

        $expectedHash = hash('sha256', 'another@example.com');
        $expectedUrl = "https://gravatar.com/avatar/{$expectedHash}?d=mp";

        expect($url)->toBe($expectedUrl);
    });
});
