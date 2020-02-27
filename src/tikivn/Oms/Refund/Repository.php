<?php
namespace Tikivn\Oms\Refund;

use Tikivn\Oms\HttpClient as OmsClient;

use GuzzleHttp\Exception\{
    ClientException,
    ServerException,
    BadResponseException
};
use Tikivn\Oms\Refund\Model\{RefundOrder, RefundOrderCollection};

class Repository
{
    private $omsClient;

    public function __construct(OmsClient $omsClient)
    {
        $this->omsClient = $omsClient;
    }

    public function findByOrderCode(string $orderCode) : RefundOrderCollection
    {
        $response = $this->omsClient->get("/v3/refund?order_code=in|{$orderCode}");
        $rawData = json_decode(strval($response->getBody()), true);

        $RefundCollection = RefundOrderCollection::hydrate($rawData['data']);

        return $RefundCollection;
    }

    public function createForCanceledOrder(string $orderCode) : ?RefundOrder
    {
        $response = $this->omsClient->post('/v3/refund?issued_by=order_canceled', ['order_code' => $orderCode]);
        $data = json_decode($response->getBody()->getContents(), true);
        $refund = new RefundOrder();
        $refund->assign($data);
        return $refund;
    }

    public function find(int $id) : ?RefundOrder
    {
        $response = $this->omsClient->get("/v3/refund/{$id}");
        $data = json_decode($response->getBody()->getContents(), true);
        $refund = new RefundOrder;
        $refund->assign($data);
        return $refund;
    }
}