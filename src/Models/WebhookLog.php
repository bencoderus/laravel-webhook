<?php

namespace Bencoderus\Webhook\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'json',
        'response' => 'json',
    ];
}
