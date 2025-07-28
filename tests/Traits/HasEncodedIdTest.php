<?php

use App\Models\Page;

it('encodes id 0 as first character', function () {
    $page = new Page;
    expect($page->encodeId(0))->toBe('0');
});

it('decodes encoded id correctly', function () {
    $page = new Page;
    $encoded = $page->encodeId(123);
    expect($page->decodeId($encoded))->toBe(123);
});

it('throws exception for invalid characters in decodeId', function () {
    $page = new Page;
    $page->decodeId('invalid@character');
})->throws(InvalidArgumentException::class, "Invalid character '@' in string");

it('handles large numbers in encoding and decoding', function () {
    $page = new Page;
    $largeId = 999999;
    $encoded = $page->encodeId($largeId);
    expect($page->decodeId($encoded))->toBe($largeId);
});

it('uses encoded_id as route key name', function () {
    $page = new Page;
    expect($page->getRouteKeyName())->toBe('encoded_id');
});
