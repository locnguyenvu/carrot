<?php
namespace App\Console\Refund;

use Tikivn\Oms\Refund\Model\{RefundOrder, CollectionRefundOrder};

class CreateForCaneledOrderCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'refund:create-for-caneled-order {orderCodes} {--exportJson}';

    public function exec($orderCodesStr) {
        $refundRepository = app('refundRepository');
        $orderCodes = explode(',', $orderCodesStr);

        $result = new CollectionRefundOrder();
        foreach ($orderCodes as $code) {
            $messageHeader = \sprintf("[%s] #%s", date('Y-m-d H:i:s'), $code);
            $refundByOrder = $refundRepository->findByOrderCode($code);
            if ($refundByOrder->count() > 0) {
                $this->result[] = [
                    'order_code' => $code,
                    'status' => 'Failed',
                    'error' => 'Refuned existed'
                ];
                echo $messageHeader.' - '.app('console_color')->apply(['red'], 'Failed'). ' - Refuned existed'.PHP_EOL;
                continue;
            }
        }
    }
}