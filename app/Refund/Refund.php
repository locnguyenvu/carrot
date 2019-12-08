<?php
namespace App\Refund;

use Carrot\Shared\Entity;
use Carrot\Shared\EntityTraitMethod;

class Refund implements Entity
{
    use EntityTraitMethod;

    protected $id;
    protected $code;
    protected $orderCode;
    protected $status;
    protected $refundAmount;
    protected $actualRefundAmount;

    
}