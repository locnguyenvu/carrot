<?php
namespace App\Console\Order;

use Tikivn\Oms\Order\Model\Order;
use Carrot\Common\{ModelToJsonTransformer};

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
        
        $transformer = new ModelToJsonTransformer();
        if ($this->hasOption('filterFields')) {
            $filterFields = array_map('trim', explode(',', $this->getOption('filterFields', '')));
            $transformer->setVisibleFields($filterFields);
        }
        echo $transformer->transform($order);
        return;
    }
}