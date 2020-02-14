<?php
namespace Tikivn\Oms\Order\Model;

class CollectionOrderEvent extends \Carrot\Common\CollectionModel
{
    protected function model() : string
    {
        return OrderEvent::class;
    }
}
