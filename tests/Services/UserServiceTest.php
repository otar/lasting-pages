<?php

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('UserService', function () {
    test('gravatarUrl generates correct URL with default parameters', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'test@example.com']);
        $userService = new UserService;

        $url = $userService->gravatarUrl($user);

        $expectedHash = hash('sha256', 'test@example.com');
        $expectedUrl = "https://gravatar.com/avatar/{$expectedHash}?d=mp";

        expect($url)->toBe($expectedUrl);
    });

    test('gravatarUrl generates correct URL with custom default', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'test@example.com']);
        $userService = new UserService;

        $url = $userService->gravatarUrl($user, 'robohash');

        $expectedHash = hash('sha256', 'test@example.com');
        $expectedUrl = "https://gravatar.com/avatar/{$expectedHash}?d=robohash";

        expect($url)->toBe($expectedUrl);
    });

    test('gravatarUrl generates correct URL with size', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'test@example.com']);
        $userService = new UserService;

        $url = $userService->gravatarUrl($user, 'mp', 200);

        $expectedHash = hash('sha256', 'test@example.com');
        $expectedUrl = "https://gravatar.com/avatar/{$expectedHash}?d=mp&s=200";

        expect($url)->toBe($expectedUrl);
    });

    test('gravatarUrl throws exception for invalid size (too small)', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'test@example.com']);
        $userService = new UserService;

        expect(fn () => $userService->gravatarUrl($user, 'mp', 0))
            ->toThrow(\Exception::class, 'Gravatar size should be between 1 and 2048.');
    });

    test('gravatarUrl throws exception for invalid size (too large)', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'test@example.com']);
        $userService = new UserService;

        expect(fn () => $userService->gravatarUrl($user, 'mp', 2049))
            ->toThrow(\Exception::class, 'Gravatar size should be between 1 and 2048.');
    });

    test('gravatarUrl accepts valid size boundaries', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'test@example.com']);
        $userService = new UserService;

        $url1 = $userService->gravatarUrl($user, 'mp', 1);
        $url2 = $userService->gravatarUrl($user, 'mp', 2048);

        $expectedHash = hash('sha256', 'test@example.com');

        expect($url1)->toBe("https://gravatar.com/avatar/{$expectedHash}?d=mp&s=1");
        expect($url2)->toBe("https://gravatar.com/avatar/{$expectedHash}?d=mp&s=2048");
    });
});
