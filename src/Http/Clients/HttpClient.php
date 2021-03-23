<?php

namespace Bencoderus\Webhook\Http\Clients;

use Bencoderus\Webhook\Exceptions\WebhookException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class HttpClient
{
    /**
     *  instance of the request headers.
     */
    private $headers;

    /**
     * An instance of the HTTP response.
     */
    private $response;

    /**
     * An instance of Guzzle Http Client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;


    /**
     * Create a new request instance.
     */
    public function __construct()
    {
        $this->client = (config('app.env') === "testing") ? $this->mockClient() : new Client(['timeout' => 15.0]);
    }

    /**
     * Mock HTTP request.
     *
     * @param int $statusCode
     * @param string $body
     * @param array $headers
     *
     * @return \GuzzleHttp\Client
     */
    private function mockClient(int $statusCode = 200, string $body = 'success', $headers = []): Client
    {
        $mock = new MockHandler([
            new Response($statusCode, $headers, $body),
        ]);

        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    /**
     * Set request headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Enable mocking for request.
     *
     * @param int $statusCode
     * @param string $body
     * @param array $headers
     *
     * @return $this
     */
    public function mock(int $statusCode = 200, string $body = '', $headers = []): self
    {
        $this->client = $this->mockClient($statusCode, $body, $headers);

        return $this;
    }

    /**
     * Send a POST request
     *
     * @param string $url
     * @param array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function post(string $url, array $data = []): self
    {
        $this->response = $this->send("POST", $url, $data);

        return $this;
    }

    /**
     * Send a request
     *
     * @param string $method
     * @param string $url
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function send(string $method, string $url, array $data)
    {
        try {
            $requestData = [];

            $requestData['json'] = $data;
            $requestData['headers'] = $this->headers;

            return $this->client->request($method, $url, $requestData);
        } catch (GuzzleException $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Handle possible request exceptions.
     *
     * @param Exception $exception
     *
     * @return mixed
     * @throws Exception
     */
    private function handleException(Exception $exception)
    {
        if ($exception instanceof RequestException && $exception->hasResponse()) {
            return $exception->getResponse();
        }

        if ($exception instanceof ConnectException) {
            throw new WebhookException("URL is currently unavailable");
        }
        if ($exception instanceof ClientException || $exception instanceof ServerException) {
            throw new WebhookException("URL is invalid or broken");
        }

        throw new WebhookException($exception->getMessage());
    }

    /**
     * Send a GET request
     *
     * @param string $url
     * @param array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function get(string $url, array $data = []): self
    {
        $this->response = $this->send("GET", $url, $data);

        return $this;
    }

    /**
     * Send a PUT request
     *
     * @param string $url
     * @param array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function put(string $url, array $data = []): self
    {
        $this->response = $this->send("PUT", $url, $data);

        return $this;
    }

    /**
     * Send a DELETE request
     *
     * @param string $url
     * @param array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function delete(string $url, array $data = []): self
    {
        $this->response = $this->send("DELETE", $url, $data);

        return $this;
    }

    /**
     * Send a PATCH request
     *
     * @param string $url
     * @param array $data
     *
     * @return $this
     * @throws \Exception
     */
    public function patch(string $url, array $data = []): self
    {
        $this->response = $this->send("PATCH", $url, $data);

        return $this;
    }

    /**
     * Return response as an array.
     *
     * @return array
     */
    public function json()
    {
        return json_decode($this->response->getBody()->getContents(), true);
    }

    /**
     * Return response status code.
     *
     * @return int
     */
    public function statusCode(): int
    {
        return $this->response->getStatusCode();
    }
}
