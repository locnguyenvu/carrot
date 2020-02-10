<?php
namespace App\Console\Order;

use Tikivn\Oms\Order\Model\Order;

class BulkViewCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:bulk-view {codes}';

    public function exec($codes) {
        $codes = array_map('trim', explode(',', $codes));
        $repository = $this->app->getService('orderRepository');

        $collectionOrder = $repository->findInCodes($codes);

        echo $collectionOrder->toJson();
    }
}