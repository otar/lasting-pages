<?php

use App\DataTransferObjects\BrowserRenderingSnapshotDto;
use App\Services\BrowserRenderingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

describe('BrowserRenderingService', function () {
    test('can take snapshot successfully', function () {
        /** @var \Tests\TestCase $this */
        Config::set('services.cf-browser-rendering', [
            'api_connection_timeout' => 30,
            'api_request_timeout' => 60,
            'api_token' => 'test-token',
            'api_url' => 'https://api.example.com',
        ]);

        Http::fake([
            'https://api.example.com/snapshot' => Http::response([
                'success' => true,
                'result' => [
                    'screenshot' => 'base64-screenshot-data',
                    'content' => '<html>Test content</html>',
                ],
            ], 200),
        ]);

        $service = new BrowserRenderingService;
        $result = $service->snapshot('https://example.com');

        expect($result)->toBeInstanceOf(BrowserRenderingSnapshotDto::class);
        expect($result->screenshot)->toBe('base64-screenshot-data');
        expect($result->html)->toBe('<html>Test content</html>');

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
            /** @var array{url: string, screenshotOptions: array{fullPage: bool}} $data */
            $data = $request->data();

            return $request->url() === 'https://api.example.com/snapshot'
                && $data['url'] === 'https://example.com'
                && $data['screenshotOptions']['fullPage'] === false;
        });
    });

    test('can take full page screenshot', function () {
        /** @var \Tests\TestCase $this */
        Config::set('services.cf-browser-rendering', [
            'api_connection_timeout' => 30,
            'api_request_timeout' => 60,
            'api_token' => 'test-token',
            'api_url' => 'https://api.example.com',
        ]);

        Http::fake([
            'https://api.example.com/snapshot' => Http::response([
                'success' => true,
                'result' => [
                    'screenshot' => 'base64-full-screenshot-data',
                    'content' => '<html>Full page content</html>',
                ],
            ], 200),
        ]);

        $service = new BrowserRenderingService;
        $result = $service->snapshot('https://example.com', true);

        expect($result)->toBeInstanceOf(BrowserRenderingSnapshotDto::class);
        expect($result->screenshot)->toBe('base64-full-screenshot-data');
        expect($result->html)->toBe('<html>Full page content</html>');

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
            /** @var array{url: string, screenshotOptions: array{fullPage: bool}} $data */
            $data = $request->data();

            return $request->url() === 'https://api.example.com/snapshot'
                && $data['url'] === 'https://example.com'
                && $data['screenshotOptions']['fullPage'] === true;
        });
    });

    test('throws exception when response fails', function () {
        /** @var \Tests\TestCase $this */
        Config::set('services.cf-browser-rendering', [
            'api_connection_timeout' => 30,
            'api_request_timeout' => 60,
            'api_token' => 'test-token',
            'api_url' => 'https://api.example.com',
        ]);

        Http::fake([
            'https://api.example.com/snapshot' => Http::response([], 500),
        ]);

        $service = new BrowserRenderingService;

        expect(fn () => $service->snapshot('https://example.com'))
            ->toThrow(Exception::class, 'Cannot take website snapshot.');
    });

    test('throws exception when response json indicates failure', function () {
        /** @var \Tests\TestCase $this */
        Config::set('services.cf-browser-rendering', [
            'api_connection_timeout' => 30,
            'api_request_timeout' => 60,
            'api_token' => 'test-token',
            'api_url' => 'https://api.example.com',
        ]);

        Http::fake([
            'https://api.example.com/snapshot' => Http::response([
                'success' => false,
                'error' => 'Invalid URL',
            ], 200),
        ]);

        $service = new BrowserRenderingService;

        expect(fn () => $service->snapshot('https://example.com'))
            ->toThrow(Exception::class, 'Invalid response JSON of the snapshot.');
    });
});
