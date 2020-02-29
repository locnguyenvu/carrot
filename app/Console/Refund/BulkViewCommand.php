<?php
namespace App\Console\Refund;

use Tikivn\Oms\Refund\Model\{RefundOrder, RefundOrderCollection};
use Carrot\Common\ModelCollectionToJsonTransformer;
use Carrot\Console\Traits\JsonHelpTrait;
use Carrot\Util\Cjson;
use Carrot\Exception\Http\{BadRequestException};

class BulkViewCommand extends \Carrot\Console\Command
{
    use JsonHelpTrait;

    protected static $pattern = 'refund:bulk-view {refundIds}';

    private $refundRepository;

    protected function init() : void {
        $this->refundRepository = app('refundRepository');
    }

    public function exec($refundIds) {
        $resultCollection = new RefundOrderCollection;

        $ids = array_map('trim', explode(',', $refundIds));
        foreach ($ids as $id) {
            $refund = $this->refundRepository->find($id);
            if (empty($refund->getId())) continue;
            $resultCollection->append($refund);
        }

        $transformer = new ModelCollectionToJsonTransformer;
        if ($this->hasOption('filterFields')) {
            $visibleFields = array_map('trim', explode(',',$this->getOption('filterFields')));
            $transformer->setVisibleFields($visibleFields);
        }

        Cjson::printWithColor($transformer->transform($resultCollection));
        return;
    }
}
