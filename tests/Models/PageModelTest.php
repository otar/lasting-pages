<?php

use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\DB;

describe('Page Model', function () {

    test('page belongs to user', function () {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $page->user);
        $this->assertEquals($user->id, $page->user->id);
    });

    test('page has correct fillable attributes', function () {
        $page = new Page;
        $fillable = $page->getFillable();

        $this->assertEquals(['user_id', 'url', 'title', 'is_pending', 'current_snapshot_id', 'current_snapshot_version'], $fillable);
    });

    test('page uses soft deletes', function () {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $page->delete();

        $freshPage = $page->fresh();
        expect($freshPage)->not->toBeNull();
        /** @var Page $freshPage */
        expect($freshPage->deleted_at)->not->toBeNull();

        $deletedCount = DB::table('pages')->where('id', $page->id)->whereNotNull('deleted_at')->count();
        expect($deletedCount)->toBe(1);
    });

    test('page casts dates correctly', function () {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $page->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $page->updated_at);
    });

    test('page has snapshots relationship', function () {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $snapshots = $page->snapshots;
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $snapshots);
    });

    test('page has current snapshot relationship', function () {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $currentSnapshot = $page->currentSnapshot;
        $this->assertNull($currentSnapshot);
    });
});
