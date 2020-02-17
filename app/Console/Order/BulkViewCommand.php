<?php
namespace App\Console\Order;

use Tikivn\Oms\Order\Model\Order;

class BulkViewCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:bulk-view {codes}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($codes) {
        $codes = array_map('trim', explode(',', $codes));
        $collectionOrder = $this->orderRepository->findInCodes($codes);
        echo $collectionOrder->toJson();
    }
}
