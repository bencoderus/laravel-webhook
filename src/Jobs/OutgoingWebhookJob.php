<?php

namespace Bencoderus\Webhook\Jobs;

use Bencoderus\Webhook\Traits\SendWebhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OutgoingWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SendWebhook;

    /**
     * The webhook data.
     *
     * @var array
     */
    public $webhookData;

    /**
     * The Number of times a webhook would be retried.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of minutes to wait before the webhook will be retried
     *
     * @var int
     */
    public $retryInterval;

    /**
     * Create a new job instance.
     *
     * @param array $webhookData
     */
    public function __construct(array $webhookData)
    {
        $this->webhookData = $webhookData;
        $this->tries = config('webhook.retries');
        $this->retryInterval = config('webhook.retry_interval');
    }

    /**
     * Process the webhook and dispatch it to the URL.
     */
    public function handle()
    {
        if (! $status = $this->sendViaHttp($this->webhookData)) {
            $this->retryWebhook();
        }
    }

    /**
     * Retry the webhook job.
     */
    private function retryWebhook()
    {
        ($this->attempts() < $this->tries)
            ? $this->release($this->retryInterval * 60)
            : $this->fail();
    }
}
