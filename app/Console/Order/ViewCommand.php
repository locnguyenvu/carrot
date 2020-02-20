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
        
        $transformer = new \Carrot\Common\ModelToArrayTransformer($order);

        if ($this->hasOption('filterFields')) {
            $filterFields = explode(',', $this->getOption('filterFields', ''));
            echo json_encode($transformer->filterFields($filterFields), JSON_PRETTY_PRINT);
            return;
        }

        echo json_encode($transformer->transform(), JSON_PRETTY_PRINT);
    }
}