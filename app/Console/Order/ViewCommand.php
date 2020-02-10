<?php
namespace App\Console\Order;

use Tikivn\Oms\Order\Model\Order;

class ViewCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:view {code}';

    public function exec($code) {
        $repository = $this->app->getService('orderRepository');
        $order = $repository->findByCode($code);
        echo $order->toJson();
    }
}