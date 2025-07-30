<?php

use App\Models\Page;
use App\Models\PageSnapshot;
use App\Models\User;
use Illuminate\Support\Facades\DB;

describe('PageSnapshot Model', function () {
    test('page snapshot belongs to page', function () {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);
        $snapshot = PageSnapshot::factory()->create(['page_id' => $page->id]);

        $this->assertInstanceOf(Page::class, $snapshot->page);
        $this->assertEquals($page->id, $snapshot->page->id);
    });

    test('page snapshot has correct fillable attributes', function () {
        $snapshot = new PageSnapshot;
        $fillable = $snapshot->getFillable();

        $this->assertEquals(['page_id', 'html_path', 'screenshot_path', 'version'], $fillable);
    });

    test('page snapshot uses soft deletes', function () {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);
        $snapshot = PageSnapshot::factory()->create(['page_id' => $page->id]);

        $snapshot->delete();

        $freshSnapshot = $snapshot->fresh();
        expect($freshSnapshot)->not->toBeNull();
        /** @var PageSnapshot $freshSnapshot */
        expect($freshSnapshot->deleted_at)->not->toBeNull();

        $deletedCount = DB::table('page_snapshots')->where('id', $snapshot->id)->whereNotNull('deleted_at')->count();
        expect($deletedCount)->toBe(1);
    });

    test('page snapshot casts version correctly', function () {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);
        $snapshot = PageSnapshot::factory()->create([
            'page_id' => $page->id,
            'version' => '5',
        ]);

        $this->assertEquals(5, $snapshot->version);
        $this->assertSame(5, $snapshot->version);
    });
});
