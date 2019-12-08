<?php
namespace App\Refund;

use Carrot\Shared\Entity;
use Carrot\Shared\EntityMethod;

class Refund implements Entity
{
    use EntityMethod;

    protected $id;
    protected $code;
    protected $orderCode;
    protected $status;
    protected $refundAmount;
    protected $actualRefundAmount;

    
}