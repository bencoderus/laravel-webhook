<?php

namespace Bencoderus\Webhook\Webhooks;

class TestWebhook extends BaseWebhook
{
    /**
     * Webhook event name.
     */
    protected $event = 'webhook.name';

    /**
     * Webhook payload.
     *
     * @return array
     */
    public function data(): array
    {
        return [
            'name' => 'John Doe',
            'age' => 18
        ];
    }
}
