<?php
namespace Oms;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\{
    ClientException,
    ServerException
};
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    private $accessToken;
    private $client;

    public function __construct(array $params = [])
    {
        $this->loadAccessToken();
        $headers = [
            'Authorization' => $this->accessToken,
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

    private function loadAccessToken() : void
    {
        $this->accessToken = file_get_contents(ROOT_PATH.'/access_token');
    }

    public function get(string $uri) : ResponseInterface
    {
        $response = $this->client->get($uri);
        return $response;
     
    }

    public function post(string $uri, array $params) : ResponseInterface
    {
        $response = $this->client->post($uri, [
            'json' => $params
        ]);
        return $response;
    }
}