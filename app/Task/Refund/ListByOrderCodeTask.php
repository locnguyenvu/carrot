<?php
namespace App\Task\Refund;

use App\Task\{AbstractTask, Task};
use Oms\Exception\RuntimeException as OmsRuntimeException;
use Oms\Refund\Repository\Apiv3Repository;
use Carrot\Carrot;

class ListByOrderCodeTask extends AbstractTask implements Task
{
    private $repository;

    //     protected function description() {
    //         var_dump([$this->hasArguments(), $this->arguments]);
    //         if ($this->hasArguments()) {
    //             return null;
    //         }
    //         return 'List danh sách hoàn tiền theo mã đơn hàng';
    //     }

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

        $result = [];
        foreach ($orderCodes as $orderCode) {
            try {
                $existedRefund = $this->repository->findRefundByOrderCode($orderCode);
                if ($existedRefund['paging']['total'] == 0) {
                    continue;
                }

                foreach ($existedRefund['data'] as $refund) { array_push($result, $refund); }

            } catch (OmsRuntimeException $e) {
                continue;
            }
        }
        echo json_encode($result);
    }
}
