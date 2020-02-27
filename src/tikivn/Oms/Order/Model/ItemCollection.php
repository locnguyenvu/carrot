<?php
namespace Tikivn\Oms\Order\Model;

class ItemCollection extends \Carrot\Common\ModelCollection
{
    protected function model() : string
    {
        return Item::class;
    }
}