<?php

namespace App\Services;

use App\DataTransferObjects\BrowserRenderingSnapshotDto;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BrowserRenderingService
{
    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws ConnectionException
     */
    protected function request(string $endpoint, array $payload = []): Response
    {
        /** @var array{api_connection_timeout: int, api_request_timeout: int, api_token: string, api_url: string} $config */
        $config = config('services.cf-browser-rendering');

        return Http::connectTimeout(seconds: $config['api_connection_timeout'])
            ->timeout(seconds: $config['api_request_timeout'])
            ->withToken($config['api_token'])
            ->contentType('application/json')
            ->post($config['api_url'].$endpoint, $payload);
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     *
     * @SuppressWarnings("PHPMD.BooleanArgumentFlag")
     */
    public function snapshot(string $websiteUrl, bool $fullPageScreenshot = false): BrowserRenderingSnapshotDto
    {
        $response = $this->request('/snapshot', [
            'url' => $websiteUrl,
            'screenshotOptions' => [
                'fullPage' => $fullPageScreenshot,
            ],
        ]);

        if ($response->failed()) {
            throw new Exception('Cannot take website snapshot.');
        }

        /** @var array{success: bool, result: array{screenshot: string, content: string}} $json */
        $json = $response->json();

        if ($json['success'] !== true) {
            throw new Exception('Invalid response JSON of the snapshot.');
        }

        return new BrowserRenderingSnapshotDto(
            screenshot: $json['result']['screenshot'],
            html: $json['result']['content']
        );
    }
}
