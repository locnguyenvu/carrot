<?php
namespace Tikivn\Oms\Refund;

use Tikivn\Oms\HttpClient as OmsClient;

use GuzzleHttp\Exception\{
    ClientException,
    ServerException,
    BadResponseException
};
use Tikivn\Oms\Refund\Model\{RefundOrder, CollectionRefundOrder};

class Repository
{
    private $omsClient;

    public function __construct(OmsClient $omsClient)
    {
        $this->omsClient = $omsClient;
    }

    public function findByOrderCode(string $orderCode) : CollectionRefundOrder
    {
        $response = $this->omsClient->get("/v3/refund?order_code=in|{$orderCode}");
        $rawData = json_decode(strval($response->getBody()), true);

        $collectionRefund = CollectionRefundOrder::hydrate($rawData['data']);

        return $collectionRefund;
    }

    public function createForCanceledOrder(string $orderCode) : array
    {
        try {
            $response = $this->omsClient->post('?issued_by=order_canceled', ['order_code' => $orderCode]);
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch (ClientException $e) {
            
        } catch (ServerException $e) {
            $response = $e->getResponse();
            
        }
    }
}