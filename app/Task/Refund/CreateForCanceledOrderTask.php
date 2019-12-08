<?php
namespace App\Task\Refund;

use App\Task\{AbstractTask, Task};
use Oms\Exception\RuntimeException as OmsRuntimeException;
use Oms\Refund\Repository\Apiv3Repository;
use Carrot\Carrot;

class CreateForCanceledOrderTask extends AbstractTask implements Task
{
    private $repository;

    protected function initialize() {
        $this->repository = new Apiv3Repository();
    }

    public function exec() : void
    {
        $rawInput = readline("Các đơn hàng: ");

        $orderCodes = array_filter(array_map('trim', explode(',', $rawInput)), 'is_numeric');
        foreach ($orderCodes as $orderCode) {
            printf("[%s] #%s", date('Y-m-d H:i:s'), $orderCode);
            try {
                // Check existen
                $existedRefund = $this->repository->findRefundByOrderCode($orderCode);
                if (array_get($existedRefund, 'paging.total', 0) >= 1) {
                    Carrot::printError("Refund has already exist");
                    continue;
                }
                // Create
                $refund = $this->repository->createForCanceledOrder($orderCode);
                Carrot::printSuccess($refund['code']);
            } catch (OmsRuntimeException $e) {
                continue;
            }
            
        }
    }
}