<?php

use App\DataTransferObjects\ReadabilityDto;
use App\Services\ReadabilityService;
use fivefilters\Readability\ParseException;

it('parses HTML content and returns ReadabilityDto', function () {
    $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Test Article</title>
            <meta name="author" content="John Doe">
        </head>
        <body>
            <article>
                <h1>Test Article Title</h1>
                <p>This is the first paragraph of the article content.</p>
                <p>This is the second paragraph with more content.</p>
            </article>
        </body>
        </html>';

    $service = new ReadabilityService;
    $result = $service->parse($html);

    expect($result)->toBeInstanceOf(ReadabilityDto::class);
    expect($result->title)->toBe('Test Article');
    expect($result->content)->toContain('Test Article Title');
    expect($result->content)->toContain('first paragraph');
    expect($result->content)->toContain('second paragraph');
    expect($result->author)->toBe('John Doe');
});

it('handles HTML without title gracefully', function () {
    $html = '<!DOCTYPE html>
        <html>
        <body>
            <article>
                <p>Content without a title.</p>
            </article>
        </body>
        </html>';

    $service = new ReadabilityService;
    $result = $service->parse($html);

    expect($result)->toBeInstanceOf(ReadabilityDto::class);
    expect($result->title)->toBe('');
    expect($result->content)->toContain('Content without a title');
});

it('handles HTML with minimal content', function () {
    $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Minimal Article</title>
        </head>
        <body>
            <p>Minimal content.</p>
        </body>
        </html>';

    $service = new ReadabilityService;
    $result = $service->parse($html);

    expect($result)->toBeInstanceOf(ReadabilityDto::class);
    expect($result->title)->toBe('Minimal Article');
    expect($result->content)->toContain('Minimal content');
});

it('throws ParseException for invalid HTML', function () {
    $html = 'This is not valid HTML';

    $service = new ReadabilityService;

    expect(fn () => $service->parse($html))->toThrow(ParseException::class);
});

it('returns author and textDirection as null when not present', function () {
    $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Simple Article</title>
        </head>
        <body>
            <article>
                <p>Simple content.</p>
            </article>
        </body>
        </html>';

    $service = new ReadabilityService;
    $result = $service->parse($html);

    expect($result)->toBeInstanceOf(ReadabilityDto::class);
    expect($result->author)->toBeNull();
    expect($result->textDirection)->toBeNull();
});
