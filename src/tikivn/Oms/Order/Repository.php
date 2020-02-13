<?php
namespace Tikivn\Oms\Order;

use Tikivn\Oms\HttpClient as OmsClient;
use Tikivn\Oms\Order\Model\{Order, CollectionOrder};

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
        try {
            $response = $this->omsClient->get('/v3/orders/' . $code);
            $result = json_decode($response->getBody()->getContents(), true);
            $order = new Order();
            $order->assign($result['order'] ?? []);
            return $order;
        } catch (BadResponseException $e) {
            throw new \Tikivn\Exception\ApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function findInCodes(array $codes) : CollectionOrder
    {
        $collection = new CollectionOrder;
        foreach ($codes as $code) {
            try {
                $response = $this->omsClient->get('/v3/orders/' . $code);
                $result = json_decode($response->getBody()->getContents(), true);
                $order = new Order();
                $order->assign($result['order'] ?? []);

                $collection->append($order);
                unset($order);
            } catch (BadResponseException $e) {
                if (in_array($e->getCode(), [401, 500, 503])) {
                    throw new \Tikivn\Exception\ApiException($e->getMessage(), $e->getCode(), $e);
                }
                continue;
            }
        }
        return $collection;
    }

    public function reindex(string $code) : array
    {
        try {
            $response = $this->omsClient->post("/v3/orders/{$code}/manage/reindex", []);
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch (BadResponseException $e) {
            if (in_array($e->getCode(), [401, 500, 503])) {
                throw new \Tikivn\Exception\ApiException($e->getMessage(), $e->getCode(), $e);
            }
            throw new \Tikivn\Exception\ToleranceException($e->getMessage);
        }
    }

    public function changeStatus(string $code, string $status, string $comment) : array
    {
        try {
            $response = $this->omsClient->post("/v3/orders/{$code}/change_status", [
                'status' => $status,
                'comment' => $comment
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
        } catch (BadResponseException $e) {
            if (in_array($e->getCode(), [401, 500, 503])) {
                throw new \Tikivn\Exception\ApiException($e->getMessage(), $e->getCode(), $e);
            }
            throw new \Tikivn\Exception\ToleranceException($e->getMessage);
        }
    }
}
