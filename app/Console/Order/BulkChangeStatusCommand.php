<?php
namespace App\Console\Order;

use Carrot\Exception\Http\{BadRequestException};
use Tikivn\Oms\Order\Model\Order;

class BulkChangeStatusCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:bulk-changestatus {codes} {status}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($codes, $status, $comment = null) {
        $codes = array_map('trim', explode(',', $codes));
        foreach ($codes as $code) {
            try {
                $messageHeader = sprintf("[%s] #%s", date('Y-m-d H:i:s'), $code);
                $this->orderRepository->changeStatus($code, $status, $comment ? : 'Carrot change status');
                $this->result[$code][] = [
                    'status' => 'Success'
                ];
                echo $messageHeader.' - '.app('console_color')->apply(['green'], 'Success');
            } catch (BadRequestException $ex) {
                $this->result[$code][] = [
                    'status' => 'Failed',
                    'error' => $ex->getMessageAsArray()
                ];
                echo $messageHeader.' - '.app('console_color')->apply(['red'], 'Failed').PHP_EOL.$ex->getMessage();
            }
            echo PHP_EOL;
        }
    }
}
