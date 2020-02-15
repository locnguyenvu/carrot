<?php
namespace App\Console\Refund;

use Tikivn\Oms\Refund\Model\{RefundOrder, CollectionRefundOrder};
use Carrot\Exception\Http\{BadRequestException};

class CreateForCaneledOrderCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'refund:create-for-caneled-order {orderCodes} {--exportJson}';

    private $refundRepository;

    protected function init() : void {
        $this->refundRepository = app('refundRepository');
    }

    public function exec($orderCodesStr) {
        $orderCodes = array_map('trim', explode(',', $orderCodesStr));
        $result = new CollectionRefundOrder();
        foreach ($orderCodes as $code) {
            $messageHeader = \sprintf("[%s] #%s", date('Y-m-d H:i:s'), $code);
            try {
                $refundOrder = $this->refundRepository->createForCanceledOrder($code);
                echo $messageHeader.' '.app('console_color')->apply(['green'], 'Success').PHP_EOL.$refundOrder->toJson().PHP_EOL;
                $this->result[] = [
                    'order' => $code,
                    'status' => 'Success',
                    'response' => $refundOrder->toArray()
                ];
            } catch (BadRequestException $e) {
                echo $messageHeader.' '.$e->getMessage().PHP_EOL; 
                $this->result[] = [
                    'order' => $code,
                    'status' => 'Failed',
                    'response' => $e->getMessageAsArray()
                ];    
                continue;
            }
        }
    }
}