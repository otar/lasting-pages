<?php

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('User Model', function () {
    test('user has many pages', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page1 = Page::factory()->create(['user_id' => $user->id]);
        $page2 = Page::factory()->create(['user_id' => $user->id]);

        $userPages = $user->pages;

        $this->assertCount(2, $userPages);
        $this->assertTrue($userPages->contains($page1));
        $this->assertTrue($userPages->contains($page2));
    });

    test('user pages relationship returns correct type', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->pages());
    });
});
