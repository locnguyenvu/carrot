<?php
namespace Tikivn\Oms\Refund\Model;

class CollectionRefundOrder extends \Carrot\Common\CollectionModel
{
    protected function model() : string
    {
        return RefundOrder::class;
    }
}
