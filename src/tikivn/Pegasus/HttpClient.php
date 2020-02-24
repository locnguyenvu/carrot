<?php
namespace Tikivn\Pegasus;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\{
    ClientException,
    ServerException,
    BadResponseException
};
use Psr\Http\Message\ResponseInterface;
use Tikivn\Authentication\AccessToken;
use Tikivn\Exception\Factory as ExceptionFactory;

class HttpClient
{
    private $client;

    public function __construct()
    {
        $this->initClient();
    }

    private function initClient()
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $baseUri = getenv('PEGASUS_DOMAIN');

        $this->client = new Client([
            'base_uri' => $baseUri,
            'headers' => $headers,
        ]);
    }

    public function get(string $uri) : ResponseInterface
    {
        try {
            $response = $this->client->get($uri);
            return $response;
        } catch (BadResponseException $e) {
            throw ExceptionFactory::make($e);
        }
    }

    public function post(string $uri, array $params) : ResponseInterface
    {
        try {
            $response = $this->client->post($uri, [
                'json' => $params
            ]);
            return $response;
        } catch (BadResponseException $e) {
            throw ExceptionFactory::make($e);
        }
    }
}