<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MeetingIntegrationClient
{
    public function isConfigured(): bool
    {
        return $this->baseUrl() !== ''
            && $this->apiKey() !== ''
            && $this->apiSecret() !== '';
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createMeeting(array $payload): array
    {
        return $this->request('POST', '/api/v1/meetings', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getMeeting(string $meetingId): array
    {
        return $this->request('GET', '/api/v1/meetings/' . rawurlencode($meetingId));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function mintJoinToken(string $meetingId, array $payload): array
    {
        return $this->request(
            'POST',
            '/api/v1/meetings/' . rawurlencode($meetingId) . '/join-tokens',
            $payload
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function endMeeting(string $meetingId): array
    {
        return $this->request('POST', '/api/v1/meetings/' . rawurlencode($meetingId) . '/end', []);
    }

    /**
     * @param  array<string, mixed>|null  $body
     * @return array<string, mixed>
     */
    protected function request(string $method, string $path, ?array $body = null): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Meeting integration is not configured.');
        }

        $method = strtoupper($method);
        $rawBody = $body === null
            ? ''
            : (string) json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $timestamp = (string) (int) round(microtime(true) * 1000);
        $signature = hash_hmac(
            'sha256',
            $timestamp . $method . $path . $rawBody,
            $this->apiSecret()
        );

        $pending = Http::baseUrl($this->baseUrl())
            ->timeout(30)
            ->acceptJson()
            ->withHeaders([
                'X-Api-Key' => $this->apiKey(),
                'X-Timestamp' => $timestamp,
                'X-Signature' => $signature,
            ]);

        if (! config('services.meeting.verify_ssl', true)) {
            $pending = $pending->withOptions(['verify' => false]);
        }

        if ($method === 'GET') {
            $response = $pending->get($path);
        } else {
            $response = $pending
                ->withBody($rawBody, 'application/json')
                ->send($method, $path);
        }

        if (! $response->successful()) {
            Log::warning('[MEETING] API request failed', [
                'method' => $method,
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException(
                'Meeting API error HTTP ' . $response->status() . ': ' . $response->body(),
                $response->status()
            );
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('services.meeting.base_url', ''), '/');
    }

    protected function apiKey(): string
    {
        return (string) config('services.meeting.api_key', '');
    }

    protected function apiSecret(): string
    {
        return (string) config('services.meeting.api_secret', '');
    }
}
