<?php
namespace App\Console\Order;

use Tikivn\Oms\Order\Model\Order;

class ViewCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:view {code}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($code) {
        $order = $this->orderRepository->findByCode($code);
        echo $order->toJson();
    }
}