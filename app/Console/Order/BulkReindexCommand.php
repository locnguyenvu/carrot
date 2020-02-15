<?php
namespace App\Console\Order;

use Carrot\Exception\Http\BadRequestException;
use Tikivn\Oms\Order\Model\Order;

class BulkReindexCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:bulk-reindex {codes} {--exportJson}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($codes) {
        $codes = array_map('trim', explode(',', $codes));
        $result = [];

        foreach ($codes as $code) {
            try {
                $this->orderRepository->reindex($code);
                printf("[%s] #%s - %s\n", date('Y-m-d H:i:s'), $code, app('console_color')->apply(['green'], 'Success'));
                $this->result[] = [
                    'code' => $code,
                    'status' => 'OK'
                ];
            } catch (BadRequestException $badResponse) {
                printf("[%s] #%s - %s\n", date('Y-m-d H:i:s'), $code, app('console_color')->apply(['red'], 'Failed'));
                $this->result[] = [
                    'code' => $code,
                    'status' => 'Failed',
                    'errors' => $badResponse->getMessageAsArray()
                ];
            }
        }
    }
}