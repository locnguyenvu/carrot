<?php
namespace Tikivn\Oms\Refund\Model;

class RefundOrder extends \Carrot\Common\Model
{
    public function assign(array $data) {
        if (isset($data['histories'])) {
            $data['histories'] = HistoryCollection::hydrate($data['histories']);
        }
        parent::assign($data);
    }
}