<?php

namespace Bencoderus\Webhook\Tests\Clients;

use Bencoderus\Webhook\Http\Clients\HttpClient;
use Bencoderus\Webhook\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class HttpClientTest extends TestCase
{
    use WithFaker;

    public function testHttpClientCanMakeAGetRequest(): void
    {
        $request = new HttpClient();
        $response = $request->get($this->faker->url);

        $this->assertSame($response->statusCode(), 200);
    }

    public function testHttpClientCanMakeAPostRequest(): void
    {
        $request = new HttpClient();
        $response = $request->post($this->faker->url);

        $this->assertSame($response->statusCode(), 200);
    }

    public function testHttpClientCanMakeAPutRequest(): void
    {
        $request = new HttpClient();
        $response = $request->put($this->faker->url);

        $this->assertSame($response->statusCode(), 200);
    }

    public function testHttpClientCanMakeADeleteRequest(): void
    {
        $request = new HttpClient();
        $response = $request->delete($this->faker->url);

        $this->assertSame($response->statusCode(), 200);
    }
}
