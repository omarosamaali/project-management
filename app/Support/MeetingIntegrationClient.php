<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class MeetingIntegrationClient
{
    public function isConfigured(): bool
    {
        return $this->baseUrl() !== ''
            && $this->apiKey() !== ''
            && $this->apiSecret() !== '';
    }

    /**
     * Lightweight connectivity + HMAC check for admin diagnostics.
     *
     * @return array{ok: bool, message: string, status?: int}
     */
    public function ping(): array
    {
        if (! $this->isConfigured()) {
            return [
                'ok' => false,
                'message' => 'MEETING_BASE_URL / MEETING_API_KEY / MEETING_API_SECRET missing.',
            ];
        }

        try {
            // Authenticated GET against a non-existent id: 404 = auth+reachability OK; 401 = bad HMAC.
            $this->request('GET', '/api/v1/meetings/__ping_probe__');

            return ['ok' => true, 'message' => 'Meeting API reachable.', 'status' => 200];
        } catch (RuntimeException $e) {
            $code = (int) $e->getCode();
            $msg = $e->getMessage();

            if ($code === 404 || str_contains($msg, 'HTTP 404')) {
                return [
                    'ok' => true,
                    'message' => 'Meeting API reachable (HMAC accepted). Host: '.$this->baseUrl(),
                    'status' => 404,
                ];
            }

            if ($code === 401 || str_contains($msg, 'HTTP 401')) {
                return [
                    'ok' => false,
                    'message' => 'Reachable but HMAC rejected — check MEETING_API_KEY / MEETING_API_SECRET match the meeting service.',
                    'status' => 401,
                ];
            }

            return [
                'ok' => false,
                'message' => $msg,
                'status' => $code > 0 ? $code : null,
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }
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
                // Free ngrok interstitial breaks JSON clients without this header.
                'ngrok-skip-browser-warning' => 'true',
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
            $body = $response->body();
            Log::warning('[MEETING] API request failed', [
                'method' => $method,
                'path' => $path,
                'status' => $response->status(),
                'body' => mb_substr($body, 0, 500),
            ]);

            if ($this->looksLikeNgrokInterstitial($body)) {
                throw new RuntimeException(
                    'ngrok browser warning blocked the API response. Server calls need header ngrok-skip-browser-warning; iframes cannot set it — use a new tab or a paid/stable tunnel.',
                    $response->status()
                );
            }

            throw new RuntimeException(
                'Meeting API error HTTP ' . $response->status() . ': ' . mb_substr($body, 0, 300),
                $response->status()
            );
        }

        $contentType = strtolower((string) $response->header('Content-Type', ''));
        $body = $response->body();
        if ($this->looksLikeNgrokInterstitial($body) || (str_contains($contentType, 'text/html') && ! str_contains($contentType, 'json'))) {
            throw new RuntimeException(
                'ngrok browser warning HTML returned instead of JSON. Add ngrok-skip-browser-warning on API calls, or avoid free-ngrok iframes.'
            );
        }

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    public function usesNgrokTunnel(): bool
    {
        $host = strtolower((string) (parse_url($this->baseUrl(), PHP_URL_HOST) ?: ''));

        return $host !== '' && (
            str_contains($host, 'ngrok-free.app')
            || str_contains($host, 'ngrok-free.dev')
            || str_contains($host, 'ngrok.io')
            || str_ends_with($host, '.ngrok.app')
        );
    }

    protected function looksLikeNgrokInterstitial(string $body): bool
    {
        $sample = strtolower(mb_substr($body, 0, 2000));

        return str_contains($sample, 'ngrok')
            && (
                str_contains($sample, 'err_ngrok')
                || str_contains($sample, 'visit site')
                || str_contains($sample, 'browser warning')
                || str_contains($sample, 'ngrok-free')
            );
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
