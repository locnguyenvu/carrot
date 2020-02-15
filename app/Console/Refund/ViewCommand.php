<?php
namespace App\Console\Refund;

use Tikivn\Oms\Refund\Model\{RefundOrder, CollectionRefundOrder};
use Carrot\Exception\Http\{BadRequestException};

class ViewCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'refund:view {refundId} {--exportJson}';

    private $refundRepository;

    protected function init() : void {
        $this->refundRepository = app('refundRepository');
    }

    public function exec($refundId) {
        $refund = $this->refundRepository->find($refundId);
        echo $refund->toJson();
    }
}