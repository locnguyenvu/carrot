<?php
namespace Tikivn\Oms\Order;

use Tikivn\Oms\HttpClient as OmsClient;
use Tikivn\Oms\Order\Model\{Order, OrderCollection, OrderEventCollection};

use GuzzleHttp\Exception\{
    ClientException,
    ServerException,
    BadResponseException
};

class Repository
{
    private $omsClient;

    public function __construct(OmsClient $omsClient) {
        $this->omsClient = $omsClient;
    }

    public function findByCode(string $code) : Order
    {
        $response = $this->omsClient->get('/v3/orders/' . $code);
        $result = json_decode($response->getBody()->getContents(), true);
        $order = new Order();
        $order->assign($result['order'] ?? []);
        return $order;
    }

    public function findInCodes(array $codes) : OrderCollection
    {
        $collection = new OrderCollection;
        foreach ($codes as $code) {
                $response = $this->omsClient->get('/v3/orders/' . $code);
                $result = json_decode($response->getBody()->getContents(), true);
                $order = new Order();
                $order->assign($result['order'] ?? []);
                $collection->append($order);
                unset($order);
        }
        return $collection;
    }

    public function reindex(string $code) : array
    {
        $response = $this->omsClient->post("/v3/orders/{$code}/manage/reindex", []);
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;
    }

    public function changeStatus(string $code, string $status, string $comment) : array
    {
        $response = $this->omsClient->post("/v3/orders/{$code}/change_status", [
            'status' => $status,
            'comment' => $comment
        ]);
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;
    }

    public function getEvents(string $code) : OrderEventCollection
    {
        $response = $this->omsClient->get("/v3/orders/{$code}/manage/events");
        $result = json_decode($response->getBody()->getContents(), true);
        return OrderEventCollection::hydrate($result);
    }
}
