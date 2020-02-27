<?php
namespace Tikivn\Oms\Order\Model;

class Order extends \Carrot\Common\Model
{
    public function assign($data) {
        $ItemCollections = ItemCollection::hydrate($data['items']);
        $data['items'] = $ItemCollections;

        $extraModel = new Extra($data['extra'] ?? null);
        $data['extra'] = $extraModel;
        
        parent::assign($data);
    }
}