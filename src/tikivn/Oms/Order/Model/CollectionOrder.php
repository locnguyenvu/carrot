<?php
namespace Tikivn\Oms\Order\Model;

class CollectionOrder extends \Carrot\Common\CollectionModel
{
    protected function model() : string
    {
        return Order::class;
    }

    public function toJson($beautify = true)
    {
        $jsonArray = [];
        foreach ($this->_data as $order) {
            $jsonArray[] = $order->toArray();
        }
        return json_encode($jsonArray, $beautify ? JSON_PRETTY_PRINT : 0);
    }
}
