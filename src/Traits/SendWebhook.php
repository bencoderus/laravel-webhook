<?php

namespace Bencoderus\Webhook\Traits;

use Bencoderus\Webhook\Exceptions\WebhookException;
use Bencoderus\Webhook\Http\Clients\HttpClient;
use Bencoderus\Webhook\Jobs\OutgoingWebhookJob;
use Bencoderus\Webhook\Models\WebhookLog;
use Exception;
use Illuminate\Support\Facades\Schema;

trait SendWebhook
{
    /**
     * Send Webhook synchronously.
     *
     * @param array $data
     *
     * @return bool
     */
    private function sendViaHttp(array $data): bool
    {
        try {
            $response = (new HttpClient())->withHeaders($data['signature'])->post($data['url'], $data['payload']);

            $this->logWebhook($response, $data);

            return $response->statusCode() >= 200 && $response->statusCode() <= 205;
        } catch (Exception $exception) {
            if ($exception instanceof WebhookException) {
                return false;
            }
        }
    }

    /**
     * Generate a webhook log.
     *
     * @param $response
     * @param array $data
     *
     * @return void
     */
    private function logWebhook($response, array $data)
    {
        if (! Schema::hasTable('webhook_logs')) {
            return;
        }

        if (! config('webhook.log_webhook')) {
            return;
        }

        $data['response'] = $response->json();
        $data['response_status_code'] = $response->statusCode();

        if (! $webhook = WebhookLog::where('uuid', $data['webhook_id'])->first()) {
            return WebhookLog::create([
                'uuid' => $data['webhook_id'],
                'url' => $data['url'],
                'response_status_code' => $data['response_status_code'],
                'response' => $data['response'],
                'payload' => $data['payload'],
            ]);
        }

        $webhook->update([
            'response_status_code' => $data['response_status_code'],
            'response' => $data['response'],
            'attempts' => $webhook->attempts + 1,
        ]);

        return $webhook;
    }

    /**
     * Send using a Queue.
     *
     * @param array $data
     *
     * @return bool
     */
    private function sendViaQueue(array $data): bool
    {
        OutgoingWebhookJob::dispatch($data);

        return true;
    }
}
