<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'event_type',
        'source',
        'payload',
        'signature',
        'is_valid',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'is_valid' => 'boolean',
        ];
    }
}
