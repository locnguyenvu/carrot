<?php
namespace App\Console\Refund;

use Tikivn\Oms\Refund\Model\{RefundOrder, CollectionRefundOrder};
use Carrot\Exception\Http\{BadRequestException};

class ListByOrderCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'refund:view-byorder {orderCode} {--exportJson}';

    private $refundRepository;

    protected function init() : void {
        $this->refundRepository = app('refundRepository');
    }

    public function exec($orderCode) {
        $refunds = $this->refundRepository->findByOrderCode($orderCode);
        echo $refunds->toJson();
    }
}