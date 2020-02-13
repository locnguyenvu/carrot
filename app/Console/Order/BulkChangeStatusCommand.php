<?php
namespace App\Console\Order;

use Tikivn\Oms\Order\Model\Order;

class BulkChangeStatusCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:bulk-changestatus {codes} {status}';

    public function exec($codes, $status, $comment = null) {
        $codes = array_map('trim', explode(',', $codes));
        $repository = $this->app->getService('orderRepository');

        $result = [];

        foreach ($codes as $code) {
            try {
                $repository->changeStatus($code, $status, $comment ? : 'Carrot change status');
                $result[$code] = 'Success';
            } catch (\Tikivn\Exception\ToleranceException $toleranceException) {
                $result[$code] = 'Failed';
            }
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
