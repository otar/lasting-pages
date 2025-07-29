<?php

namespace App\Services;

readonly class BrowserRenderingSnapshotDto
{
    public function __construct(
        public string $screenshot,
        public string $html
    ) {}
}
