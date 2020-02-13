<?php
namespace App\Console\Order;

use Tikivn\Oms\Order\Model\Order;

class BulkReindexCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:bulk-reindex {codes} {--exportJson}';

    public function exec($codes) {
        $codes = array_map('trim', explode(',', $codes));
        $repository = $this->app->getService('orderRepository');

        $result = [];

        foreach ($codes as $code) {
            try {
                $repository->reindex($code);
                printf("[%s] #%s - %s\n", date('Y-m-d H:i:s'), $code, app('console_color')->apply(['green'], 'Success'));
                $this->result[] = [
                    'code' => $code,
                    'status' => 'OK'
                ];
            } catch (\Tikivn\Exception\ToleranceException $toleranceException) {
                printf("[%s] #%s - %s\n", date('Y-m-d H:i:s'), $code, app('console_color')->apply(['red'], 'Failed'));
                $this->result[] = [
                    'code' => $code,
                    'status' => 'Failed',
                    'errors' => $toleranceException->getMessage()
                ];
            }
        }
    }
}