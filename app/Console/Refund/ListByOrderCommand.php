<?php
namespace App\Console\Refund;

use Tikivn\Oms\Refund\Model\{RefundOrder, RefundOrderCollection};
use Carrot\Common\{ModelCollectionToJsonTransformer};
use Carrot\Console\Traits\JsonHelpTrait;
use Carrot\Exception\Http\{BadRequestException};

class ListByOrderCommand extends \Carrot\Console\Command
{
    use JsonHelpTrait;

    protected static $pattern = 'refund:list-byorder {orderCodes}';

    private $refundRepository;

    protected function init() : void {
        $this->refundRepository = app('refundRepository');
    }

    public function exec($orderCodes) {
        $orders = array_map('trim', explode(',', $orderCodes));
        $resultCollection = new RefundOrderCollection();
        foreach ($orders as $order) {
            $refunds = $this->refundRepository->findByOrderCode($order);
            $resultCollection->join($refunds);
        }

        $transformer = new ModelCollectionToJsonTransformer();
        if ($this->hasOption('filterFields')) {
            $fields = array_map('trim', explode(',',$this->getOption('filterFields')));
            $transformer->setVisibleFields($fields);
        }
        echo $transformer->transform($resultCollection);
    }
}
