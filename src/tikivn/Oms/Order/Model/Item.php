<?php
namespace Tikivn\Oms\Order\Model;

class Item extends \Carrot\Common\Model
{

    public function assign($data) {
        $extra = new Item\Extra($data['extra'] ?? '');

        $data['extra'] = $extra;
        parent::assign($data);
    }
}
