<?php
namespace App\Console\Order;

use Tikivn\Oms\Order\Model\Order;

class BulkReindexCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:bulk-reindex {codes}';

    public function exec($codes) {
        $codes = array_map('trim', explode(',', $codes));
        $repository = $this->app->getService('orderRepository');

        $result = [];

        foreach ($codes as $code) {
            try {
                $repository->reindex($code);
                $result[$code] = 'Success';
            } catch (\Tikivn\Exception\ToleranceException $toleranceException) {
                $result[$code] = 'Failed';
            }
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}