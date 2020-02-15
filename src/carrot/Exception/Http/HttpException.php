<?php
namespace Carrot\Exception\Http;

use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpException extends \RuntimeException
{
    protected $responseBody;
    protected $statusCode;
    protected $reasonPhrase;

    public function __construct(BadResponseException $badResponseExpcetion) {
        
        $httpResponse = $badResponseExpcetion->getResponse();

        $this->responseBody = $httpResponse->getBody()->getContents();
        $this->statusCode = $httpResponse->getStatusCode();
        $this->reasonPhrase = $httpResponse->getReasonPhrase();

        $message = $this->beautifyMessage();
        $code = $badResponseExpcetion->getCode();

        parent::__construct($message, $code, $badResponseExpcetion);
    }

    public function beautifyMessage() : string
    {
        $colorConsole = new \JakubOnderka\PhpConsoleColor\ConsoleColor;
        $header = $colorConsole->apply(['red'], sprintf(
            "%s - %s", 
            $this->statusCode, 
            $this->reasonPhrase
        ));

        $responseBody = $this->responseBody;

        $decodeBody = \json_decode($responseBody, true);
        if (\json_last_error() !== JSON_ERROR_NONE) {
            return $header.PHP_EOL.$responseBody;
        }
        return $header.PHP_EOL.json_encode($decodeBody, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function getMessageAsArray() : array
    {
        $responseBody = $this->responseBody;

        $decodeBody = \json_decode($responseBody, true);
        if (\json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => $responseBody];
        }
        return $decodeBody;
    }
}