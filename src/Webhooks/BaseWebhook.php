<?php

namespace Bencoderus\Webhook\Webhooks;

use Bencoderus\Webhook\Exceptions\WebhookException;
use Bencoderus\Webhook\Traits\SendWebhook;
use Illuminate\Http\Resources\DelegatesToResource;
use Illuminate\Support\Str;

abstract class BaseWebhook
{
    use SendWebhook, DelegatesToResource;

    /**
     * Webhook encryption signature.
     *
     * @var array
     */
    public $signature;

    /**
     * The URL the outgoing will be dispatched to.
     *
     * @var string
     */
    private $url;

    /**
     * The data that will be dispatched to the outgoing webhook.
     *
     * @var array
     */
    private $payload;

    /**
     * An instance of the webhook payload.
     *
     * @var null
     */
    private $resource;

    /**
     * Create a webhook instance.
     *
     * @param null $model
     */
    public function __construct($model = null)
    {
        $this->resource = $model;
    }

    /**
     * Add a webhook signature.
     *
     * @param string $signatureName
     * @param string $encryptionKey
     * @param string $hashAlgorithm
     *
     * @return $this
     */
    public function withSignature(string $signatureName, string $encryptionKey, string $hashAlgorithm = 'sha512'): self
    {
        $encryptionKey = hash($hashAlgorithm, $encryptionKey);

        $this->signature = [$signatureName => $encryptionKey];

        return $this;
    }

    /**
     * Add webhook url.
     *
     * @param string $url
     *
     * @return $this
     */
    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Send webhook asynchronously(Queue) or synchronously.
     *
     * @param bool $sendWithQueue
     *
     * @return bool
     * @throws \Bencoderus\Webhook\Exceptions\WebhookException
     */
    public function send(bool $sendWithQueue = true): bool
    {
        $data = $this->prepareWebhook();

        if (empty($data['url'])) {
            throw new WebhookException("You need to set a webhook URL");
        }

        if (! config('webhook.dispatch_webhook')) {
            return true;
        }

        if ($sendWithQueue) {
            return $this->sendViaQueue($data);
        }

        return $this->sendViaHttp($data);
    }

    /**
     * @return array
     */
    public function prepareWebhook(): array
    {
        $webhookData = [];

        $this->generatePayload();

        if ($this->signature) {
            $webhookData['signature'] = $this->signature ?? [];
        }

        $webhookData['url'] = $this->url;
        $webhookData['payload'] = $this->payload;
        $webhookData['webhook_id'] = Str::uuid();

        return $webhookData;
    }

    /**
     * @return array
     */
    private function generatePayload(): array
    {
        $this->payload = array_merge(
            ['event' => $this->event],
            ['data' => $this->data()]
        );

        return $this->payload;
    }

    /**
     * @return mixed
     */
    abstract public function data();

}
