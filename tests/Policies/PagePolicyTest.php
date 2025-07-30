<?php

use App\Models\Page;
use App\Models\User;
use App\Policies\PagePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

describe('Page Policy', function () {
    beforeEach(function () {
        Queue::fake();
    });
    test('user can view their own page', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);
        $policy = new PagePolicy;

        $this->assertTrue($policy->view($user, $page));
    });

    test('user cannot view another users page', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $otherUser->id]);
        $policy = new PagePolicy;

        $this->assertFalse($policy->view($user, $page));
    });

    test('authenticated user can create pages', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $policy = new PagePolicy;

        $this->assertTrue($policy->create($user));
    });

    test('user with null id cannot create pages', function () {
        /** @var \Tests\TestCase $this */
        $user = new User;
        $policy = new PagePolicy;

        $this->assertFalse($policy->create($user));
    });

    test('user can delete their own page', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);
        $policy = new PagePolicy;

        $this->assertTrue($policy->delete($user, $page));
    });

    test('user cannot delete another users page', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $otherUser->id]);
        $policy = new PagePolicy;

        $this->assertFalse($policy->delete($user, $page));
    });
});
