<?php
namespace App\Refund;

class RefundCollection extends \Carrot\Shared\EntityCollection
{
    public function getEntityClass() : string
    {
        return Refund::class;
    }
}