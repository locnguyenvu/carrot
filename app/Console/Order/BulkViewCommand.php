<?php
namespace App\Console\Order;

use Carrot\Common\{CollectionModelToJsonTransformer};
use Carrot\Console\Traits\JsonHelpTrait;
use Tikivn\Oms\Order\Model\Order;

class BulkViewCommand extends \Carrot\Console\Command
{
    use JsonHelpTrait;

    protected static $pattern = 'order:bulk-view {codes}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($codes) {
        $codes = array_map('trim', explode(',', $codes));
        $collectionOrder = $this->orderRepository->findInCodes($codes);

        $transformer = new CollectionModelToJsonTransformer();
        if ($this->hasOption('filterFields')) {
            $fields = array_map('trim', explode(',', $this->getOption('filterFields')));
            $transformer->setVisibleFields($fields);
        }
        echo $transformer->transform($collectionOrder);
    }
}
