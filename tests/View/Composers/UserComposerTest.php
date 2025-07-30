<?php

use App\Models\User;
use App\Services\UserService;
use App\View\Composers\UserComposer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\View;

uses(RefreshDatabase::class);

describe('UserComposer', function () {
    test('composes user data when user is authenticated', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create(['email' => 'test@example.com']);
        $this->actingAs($user);

        $userService = app(UserService::class);
        $composer = new UserComposer($userService);

        $view = \Mockery::mock(View::class);
        // @phpstan-ignore method.notFound,method.nonObject
        $view->shouldReceive('with')
            ->once()
            ->with([
                'userEmail' => 'test@example.com',
                'userAvatar' => $userService->gravatarUrl($user, size: 32),
            ]);

        // @phpstan-ignore argument.type
        $composer->compose($view);
    });

    test('does not compose data when user is not authenticated', function () {
        /** @var \Tests\TestCase $this */
        $userService = app(UserService::class);
        $composer = new UserComposer($userService);

        $view = \Mockery::mock(View::class);
        $view->shouldNotReceive('with');

        // @phpstan-ignore argument.type
        $composer->compose($view);
    });
});
