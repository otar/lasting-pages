<?php

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Page Model', function () {
    test('page belongs to user', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $page->user);
        $this->assertEquals($user->id, $page->user->id);
    });

    test('page has correct fillable attributes', function () {
        /** @var \Tests\TestCase $this */
        $page = new Page;
        $fillable = $page->getFillable();

        $this->assertEquals(['user_id', 'url', 'title'], $fillable);
    });

    test('page uses soft deletes', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $page->delete();

        $this->assertSoftDeleted('pages', ['id' => $page->id]);

        $freshPage = $page->fresh();
        $this->assertNotNull($freshPage);
        $this->assertNotNull($freshPage->deleted_at);
    });

    test('page casts dates correctly', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $page->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $page->updated_at);
    });
});
