<?php
namespace Tikivn\Oms\Order\Model;

class CollectionItem extends \Carrot\Common\CollectionModel
{
    protected function model() : string
    {
        return Item::class;
    }
}