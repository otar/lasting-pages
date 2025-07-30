<?php

namespace App\DataTransferObjects;

readonly class BrowserRenderingSnapshotDto
{
    public function __construct(
        public string $screenshot,
        public string $html
    ) {}
}
