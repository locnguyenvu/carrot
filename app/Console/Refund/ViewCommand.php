<?php
namespace App\Console\Refund;

use Tikivn\Oms\Refund\Model\{RefundOrder, CollectionRefundOrder};
use Carrot\Common\ModelToJsonTransformer;
use Carrot\Console\Traits\JsonHelpTrait;
use Carrot\Exception\Http\{BadRequestException};

class ViewCommand extends \Carrot\Console\Command
{
    use JsonHelpTrait;

    protected static $pattern = 'refund:view {refundId}';

    private $refundRepository;

    protected function init() : void {
        $this->refundRepository = app('refundRepository');
    }

    public function exec($refundId) {
        $refund = $this->refundRepository->find($refundId);

        $transformer = new ModelToJsonTransformer;
        if ($this->hasOption('filterFields')) {
            $visibleFields = array_map('trim', explode(',',$this->getOption('filterFields')));
            $transformer->setVisibleFields($visibleFields);
        }

        echo $transformer->transform($refund);
        return;
    }
}
