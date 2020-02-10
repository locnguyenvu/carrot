<?php
namespace Tikivn\Exception;

class ApiException extends \Exception{

    public $statusCode;
    public $rawBodyResponse;

    public function getDetailAsArray() : array
    {
        $response = $this->getPrevious()->getResponse();
        
        $responseBody = (string)$response->getBody();
        $tryJsonDecode = json_decode($responseBody, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $detailInfo = (string)$response->getBody();
        } else {
            $detailInfo = $tryJsonDecode;
        }

        return [
            'status_code' => $this->code,
            'summary' => $this->message,
            'detail' => $detailInfo,
        ];
    }
}