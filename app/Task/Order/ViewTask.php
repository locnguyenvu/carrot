<?php
namespace App\Task\Order;

use App\Task\{AbstractTask, Task};
use Oms\Exception\RuntimeException as OmsRuntimeException;
use Oms\Order\Repository\Apiv3Repository;
use Carrot\Carrot;

class ViewTask extends AbstractTask implements Task
{
    private $repository;
    private $orderCodes = [];

    protected function description() {
        return null;
    }

    protected function initialize() {
        $this->repository = new Apiv3Repository();
    }

    public function exec() : void
    {
        $result = [];
        foreach ($this->orderCodes as $ocode) {
            $order = array_get($this->repository->get($ocode), 'order', null);
            ksort($order);
            $result[] = $order;
        }
        echo json_encode($result);

    }

    public function readArguments(array $shellArguments) : void
    {
        $this->orderCodes = array_unique(array_filter(explode(',',$shellArguments[0]), 'is_numeric'));
    }
}
