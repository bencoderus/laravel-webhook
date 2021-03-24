<?php

/*
 * You can place your custom package configuration in here.
 */

return [
    /**
     *  Enable/Disable sending out of webhooks.
     */
    'dispatch_webhook' => env('WEBHOOK_ENABLE_SEND', true),

    /**
     * Enable webhook log.
     */
    'log_webhook' => env('WEBHOOK_ENABLE_LOG', true),

    /**
     * The number of times the webhook would be retried after failure (! 200, 201, 202, 205).
     */

    'retries' => env('WEBHOOK_RETRIES', 3),

    /**
     * Webhook retry interval (The number of minutes to wait before retrying the webhook after failure).
     */
    'retry_interval' => env('WEBHOOK_RETRY_INTERVAL', 15),
];
