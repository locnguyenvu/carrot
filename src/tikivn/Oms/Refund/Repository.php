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
        try {
            $response = $this->omsClient->get("/v3/refund?order_code=in|{$orderCode}");
            $rawData = json_decode(strval($response->getBody()), true);

            $collectionRefund = CollectionRefundOrder::hydrate($rawData['data']);

            return $collectionRefund;
        } catch (BadResponseException $e) {
            throw \Tikivn\Exception\Factory::make($e);
        }
    }

    public function createForCanceledOrder(string $orderCode) : ?RefundOrder
    {
        try {
            $response = $this->omsClient->post('/v3/refund?issued_by=order_canceled', ['order_code' => $orderCode]);
            $data = json_decode($response->getBody()->getContents(), true);
            $refund = new RefundOrder();
            $refund->assign($data);
            return $refund;
        } catch (BadResponseException $e) {
            throw \Tikivn\Exception\Factory::make($e);
        }
    }
}