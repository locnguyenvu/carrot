<?php
namespace Tikivn\Oms\Order\Model;

class Order extends \Carrot\Common\Model
{
    public function assign($data) {
        $collectionItems = CollectionItem::hydrate($data['items']);
        $data['items'] = $collectionItems;

        $extraModel = new Extra($data['extra'] ?? null);
        $data['extra'] = $extraModel;
        
        parent::assign($data);
    }
}