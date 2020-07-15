<?php
namespace App\Console\Order;

use Carrot\Common\{ModelCollectionToJsonTransformer};
use Carrot\Console\Traits\JsonHelpTrait;
use Carrot\Util\Cjson;
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
        $OrderCollection = $this->orderRepository->findInCodes($codes);

        $transformer = new ModelCollectionToJsonTransformer();
        if ($this->hasOption('filterFields')) {
            $fields = array_map('trim', explode(',', $this->getOption('filterFields')));
            $transformer->setVisibleFields($fields);
        }
        if ($this->hasOption('nocolor')) {
            echo $transformer->transform($OrderCollection);
        } else {
            Cjson::printWithColor($transformer->transform($OrderCollection));
        }
    }
}
