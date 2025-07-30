<?php

namespace App\DataTransferObjects;

readonly class ReadabilityDto
{
    public function __construct(
        public string $title,
        public string $content,
        public ?string $excerpt = null,
        public ?string $author = null,
        public ?string $textDirection = null,
        // public string $mainImage,
        // public string $allImages,
    ) {}
}
