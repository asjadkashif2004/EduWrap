<?php

namespace App\Repositories;

use App\Models\WebhookLog;

class WebhookLogRepository
{
    public function create(
        string $eventType,
        string $source,
        array $payload,
        ?string $signature,
        bool $isValid
    ): WebhookLog {
        return WebhookLog::create([
            'event_type' => $eventType,
            'source' => $source,
            'payload' => $payload,
            'signature' => $signature,
            'is_valid' => $isValid,
        ]);
    }
}
