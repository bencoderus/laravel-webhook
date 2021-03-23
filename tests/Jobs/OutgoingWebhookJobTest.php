<?php

namespace Bencoderus\Webhook\Tests\Jobs;

use Bencoderus\Webhook\Jobs\OutgoingWebhookJob;
use Bencoderus\Webhook\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;


class OutgoingWebhookJobTest extends TestCase
{
    use WithFaker;

    public function testOutgoingWebhookIsBeingDispatched()
    {
        Bus::fake();

        $data = [
            'url' => $this->faker->url,
            'payload' => [
                'event' => 'webhook.sent',
                'data' => 'hello'
            ],
        ];

        OutgoingWebhookJob::dispatch($data);

        Bus::assertDispatched(OutgoingWebhookJob::class, function ($job) use ($data) {
            return $job->webhookData['url'] === $data['url'] && count($job->webhookData['payload']) === count($data['payload']);
        });
    }
}
