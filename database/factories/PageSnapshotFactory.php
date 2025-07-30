<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageSnapshot>
 */
class PageSnapshotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => \App\Models\Page::factory(),
            'html_path' => 'page-snapshots/1/html/example_'.now()->format('Y-m-d_H-i-s').'_v1.html',
            'screenshot_path' => 'page-snapshots/1/screenshots/example_'.now()->format('Y-m-d_H-i-s').'_v1.png',
            'version' => 1,
        ];
    }
}
