<?php
namespace Tikivn\Oms;

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
    private $accessToken;
    private $client;

    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;

        $this->initClient();
    }

    private function initClient()
    {
        $headers = [
            'Authorization' => $this->accessToken->getJwt(),
            'Content-Type' => 'application/json'
        ];
        $baseUri = getenv('OMS_DOMAIN');
        
        if (!empty($params['base_uri'])) {
            $baseUri .= '/'.$params['base_uri'];
        }

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