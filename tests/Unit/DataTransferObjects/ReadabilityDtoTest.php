<?php

use App\DataTransferObjects\ReadabilityDto;

it('creates ReadabilityDto with all properties', function () {
    $dto = new ReadabilityDto(
        title: 'Test Title',
        content: 'Test Content',
        excerpt: 'Test Excerpt',
        author: 'Test Author',
        textDirection: 'ltr'
    );

    expect($dto->title)->toBe('Test Title');
    expect($dto->content)->toBe('Test Content');
    expect($dto->excerpt)->toBe('Test Excerpt');
    expect($dto->author)->toBe('Test Author');
    expect($dto->textDirection)->toBe('ltr');
});

it('creates ReadabilityDto with required properties only', function () {
    $dto = new ReadabilityDto(
        title: 'Required Title',
        content: 'Required Content'
    );

    expect($dto->title)->toBe('Required Title');
    expect($dto->content)->toBe('Required Content');
    expect($dto->excerpt)->toBeNull();
    expect($dto->author)->toBeNull();
    expect($dto->textDirection)->toBeNull();
});

it('is readonly', function () {
    $dto = new ReadabilityDto(
        title: 'Readonly Title',
        content: 'Readonly Content'
    );

    $reflection = new ReflectionClass($dto);
    expect($reflection->isReadOnly())->toBeTrue();
});
