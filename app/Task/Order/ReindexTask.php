<?php
namespace App\Task\Order;

use App\Task\{AbstractTask, Task};
use Oms\Exception\RuntimeException as OmsRuntimeException;
use Oms\Order\Repository\Apiv3Repository;
use Carrot\Carrot;

class ReindexTask extends AbstractTask implements Task
{
    private $repository;

    protected function description() {
        return 'Reindex đơn hàng';
    }

    protected function initialize() {
        $this->repository = new Apiv3Repository();
    }

    public function exec() : void
    {
        if ($this->hasArgument('orders')) {
            $rawInput = $this->getArgument('orders');
        } else {
            $rawInput = readline("Mã các đơn hàng cách nhau bằng dầu phẩy: ");
        }

        $orderCodes = array_filter(array_map('trim', explode(',', $rawInput)), 'is_numeric');
        foreach ($orderCodes as $orderCode) {
            try {
                printf("[%s] #%s", date('Y-m-d H:i:s'), $orderCode);
                $result = $this->repository->reindex($orderCode);
                Carrot::printSuccess($result['success'] ? 'Success':'Done');
            } catch (OmsRuntimeException $e) {
                continue;
            }
        }
    }
}