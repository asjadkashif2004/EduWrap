<?php

namespace App\Services;

use App\Repositories\WebhookLogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public function __construct(
        private readonly WebhookLogRepository $webhookLogRepository,
    ) {
    }

    public function verifySignature(Request $request): bool
    {
        $secret = config('services.webhook.secret');
        $signature = (string) $request->header('X-Webhook-Signature');

        if (!$secret || !$signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }

    public function log(string $eventType, string $source, array $payload, ?string $signature, bool $isValid): void
    {
        $this->webhookLogRepository->create(
            eventType: $eventType,
            source: $source,
            payload: $payload,
            signature: $signature,
            isValid: $isValid
        );
    }

    public function dispatchInternalEvent(string $eventType, array $payload): void
    {
        $secret = (string) config('services.webhook.secret');
        $eventPayload = [
            'event_type' => $eventType,
            ...$payload,
        ];
        $body = json_encode($eventPayload) ?: '{}';
        $signature = hash_hmac('sha256', $body, $secret);

        try {
            Http::timeout(2)
                ->withHeaders(['X-Webhook-Signature' => $signature])
                ->post(rtrim(config('app.url'), '/').'/api/webhook', $eventPayload);
        } catch (\Throwable $exception) {
            Log::warning('Internal webhook dispatch failed. Falling back to local log.', [
                'event_type' => $eventType,
                'error' => $exception->getMessage(),
            ]);

            $this->log($eventType, 'internal', $eventPayload, $signature, true);
        }
    }
}
