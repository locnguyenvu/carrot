<?php
namespace App\Console\Product;

use Carrot\Common\ModelCollectionToJsonTransformer;

class BulkViewCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'product:bulk-view {ids|skus} {{--sku}}';

    private $catalogRepository;

    protected function init() : void
    {
        $this->catalogRepository = app('catalogRepository');
    }

    public function exec($ids) {
        $idArr = array_map('intval', explode(',', $ids));
        if ($this->hasOption('sku')) {
            $collectionProduct = $this->catalogRepository->findBySkus($idArr);
        } else {
            $collectionProduct = $this->catalogRepository->findByIds($idArr);
        }

        $transformer = new ModelCollectionToJsonTransformer();
        if ($this->hasOption('filterFields')) {
            $fields = array_map('trim', explode(',', $this->getOption('filterFields')));
            $transformer->setVisibleFields($fields);
        }
        echo $transformer->transform($collectionProduct);
    }
}