<?php
namespace App\Console\Refund;

use Carrot\Common\{ModelToArrayTransformer};
use Carrot\Exception\Http\{BadRequestException};
use Carrot\Util\Cjson;
use Tikivn\Oms\Refund\Model\{RefundOrder, RefundOrderCollection};

class CreateForCaneledOrderCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'refund:create-for-caneled-order {orderCodes}';

    private $refundRepository;

    protected function init() : void {
        $this->refundRepository = app('refundRepository');
    }

    public function exec($orderCodesStr) {
        $orderCodes = array_map('trim', explode(',', $orderCodesStr));
        $mtaTransformer = new ModelToArrayTransformer();
        $mtaTransformer->setVisibleFields([
            'code', 
            'order_code',
            'refund_amount',
            'merchant_ref_code',
            'created_at'
        ]);
        $result = new RefundOrderCollection();
        foreach ($orderCodes as $code) {
            $messageHeader = \sprintf("[%s] #%s", date('Y-m-d H:i:s'), $code);
            try {
                $refundOrder = $this->refundRepository->createForCanceledOrder($code);
                echo $messageHeader.' '.app('console_color')->apply(['green'], 'Success').' >>> '.sprintf('%s [%d]', $refundOrder->getCode(), $refundOrder->getRefundAmount());
                $this->result[] = [
                    'order' => $code,
                    'status' => 'Success',
                    'response' => $mtaTransformer->transform($refundOrder)
                ];
            } catch (BadRequestException $e) {
                echo $messageHeader.' '.$e->getMessage(); 
                $this->result[] = [
                    'order' => $code,
                    'status' => 'Failed',
                    'response' => $e->getMessageAsArray()
                ];
            }
            echo PHP_EOL;
        }
    }
}