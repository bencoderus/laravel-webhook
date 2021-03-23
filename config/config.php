<?php

/*
 * You can place your custom package configuration in here.
 */

return [
    /**
     *  Enable/Disable sending out of webhooks.
     */
    'dispatch_webhook' => true,

    /**
     * Enable webhook log.
     */
    'log_webhook' => true,

    /**
     * The number of times the webhook would be retried after failure (! 200, 201, 202, 205).
     */

    'retries' => 3,

    /**
     * Webhook retry interval (The number of minutes to wait before retrying the webhook after failure).
     */
    'retry_interval' => 10,
];
