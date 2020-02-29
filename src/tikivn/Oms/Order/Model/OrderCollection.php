<?php
namespace Tikivn\Oms\Order\Model;

class OrderCollection extends \Carrot\Common\ModelCollection
{
    protected function model() : string
    {
        return Order::class;
    }
}
