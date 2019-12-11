<?php
namespace Oms\Refund\Repository;

use Oms\HttpClient;
use Oms\Exception\RuntimeException as OmsRuntimeException;
use GuzzleHttp\Exception\{
    ClientException,
    ServerException,
    BadResponseException
};
use Carrot\Carrot;

class Apiv3Repository implements Repository
{
    const BASE_URI = 'v3/refund';
    private $omsClient;
    
    public function __construct()
    {
        $this->omsClient = new HttpClient([
            'base_uri' => static::BASE_URI
        ]);
    }

    public function createForCanceledOrder(string $orderCode) : array
    {
        try {
            $response = $this->omsClient->post('?issued_by=order_canceled', ['order_code' => $orderCode]);
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch (ClientException $e) {
            static::printOmsErrorMessage($e);
            throw new OmsRuntimeException('Error');
        } catch (ServerException $e) {
            $response = $e->getResponse();
            Carrot::printError($response->getBody()->getContents());
            throw new OmsRuntimeException('Error');
        }
    }

    public function findRefundByOrderCode(string $orderCode) : array
    {
        try {
            $response = $this->omsClient->get('?order_code='.$orderCode);
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch (ClientException $e) {
            static::printOmsErrorMessage($e);
            throw new OmsRuntimeException('Error');
        } catch (ServerException $e) {
            $response = $e->getResponse();
            Carrot::printError($response->getBody()->getContents());
            throw new OmsRuntimeException('Error');
        }
    }

    public static function printOmsErrorMessage(BadResponseException $e) : void 
    {
        $response = $e->getResponse();
        $error = json_decode($response->getBody()->getContents(), true, 512, JSON_UNESCAPED_UNICODE);
        $errorMessage = array_get($error, 'error.message');
        Carrot::printError($errorMessage);
    }
}