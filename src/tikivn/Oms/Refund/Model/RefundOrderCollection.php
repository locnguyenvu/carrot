<?php
namespace Tikivn\Oms\Refund\Model;

class RefundOrderCollection extends \Carrot\Common\ModelCollection
{
    protected function model() : string
    {
        return RefundOrder::class;
    }
}
