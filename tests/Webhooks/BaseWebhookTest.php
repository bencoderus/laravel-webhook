<?php

namespace Bencoderus\Webhook\Tests\Webhooks;

use Bencoderus\Webhook\Http\Clients\HttpClient;
use Bencoderus\Webhook\Jobs\OutgoingWebhookJob;
use Bencoderus\Webhook\Tests\TestCase;
use Bencoderus\Webhook\Traits\SendWebhook;
use Bencoderus\Webhook\Webhooks\TestWebhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;

class BaseWebhookTest extends TestCase
{
    use WithFaker, RefreshDatabase, SendWebhook;

    private $defaultHashAlgorithm = "sha512";

    public function testWebhookSignatureIsSet()
    {
        $webhook = new TestWebhook();
        $encryptionKey = $this->faker->md5;

        $data = $webhook->withSignature('x-key', $encryptionKey)->prepareWebhook();
        $this->assertArrayHasKey('signature', $data);
        $this->assertArrayHasKey('x-key', $data['signature']);
        $this->assertEquals($data['signature']['x-key'], hash($this->defaultHashAlgorithm, $encryptionKey));
    }

    public function testWebhookUrlIsSet()
    {
        $url = $this->faker->url;
        $webhook = new TestWebhook();
        $data = $webhook->url($url)->prepareWebhook();

        $this->assertEquals($data['url'], $url);
        $this->assertIsString($data['url']);
    }

    public function testWebhookDefaultPayloadIsReturned()
    {
        $url = $this->faker->url;
        $webhook = new TestWebhook();
        $data = $webhook->url($url)->prepareWebhook();

        $this->assertArrayHasKey('name', $data['payload']['data']);
        $this->assertArrayHasKey('age', $data['payload']['data']);
    }

    public function testWebhookIsPreparedBeforeBeingSent()
    {
        $url = $this->faker->url;
        $webhook = new TestWebhook();
        $data = $webhook
            ->url($url)
            ->withSignature('x-key', $this->faker->md5)
            ->prepareWebhook();

        $this->assertArrayHasKey('url', $data);
        $this->assertArrayHasKey('payload', $data);
        $this->assertArrayHasKey('signature', $data);
    }

    public function testWebhookIsBeingQueued()
    {
        Bus::fake();

        $webhook = new TestWebhook();
        $webhook
            ->url($this->faker->url)
            ->withSignature('x-key', $this->faker->md5)
            ->send();

        Bus::assertDispatched(OutgoingWebhookJob::class);
    }

    public function testWebhookIsBeingSent()
    {
        $url = $this->faker->url;

        $webhookSent = (new TestWebhook())
            ->url($url)
            ->withSignature('x-key', $this->faker->md5)
            ->send(false);

        $this->assertTrue($webhookSent);
        $this->assertDatabaseHas('webhook_logs', [
            'url' => $url
        ]);
    }

    public function testWebhookIsLoggedWhenEnabled()
    {
        config(['webhook.log_webhook' => true]);

        $url = $this->faker->url;

        $request = new HttpClient();
        $response = $request->get($this->faker->url);

        $webhook = new TestWebhook();
        $data = $webhook
            ->url($url)
            ->withSignature('x-key', $this->faker->md5)
            ->prepareWebhook();

        $this->logWebhook($response, $data);

        $this->assertDatabaseHas('webhook_logs', [
            'response_status_code' => $response->statusCode(),
            'url' => $url
        ]);
    }

    public function testWebhookIsNotLoggedWhenDisabled()
    {
        config(['webhook.log_webhook' => false]);

        $url = $this->faker->url;

        $request = new HttpClient();
        $response = $request->get($this->faker->url);

        $webhook = new TestWebhook();
        $data = $webhook
            ->url($url)
            ->withSignature('x-key', $this->faker->md5)
            ->prepareWebhook();

        $this->logWebhook($response, $data);

        $this->assertDatabaseMissing('webhook_logs', [
            'response_status_code' => $response->statusCode(),
            'url' => $url
        ]);
    }
}
