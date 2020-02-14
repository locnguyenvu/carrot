<?php
namespace App\Console\Order;

use LucidFrame\Console\ConsoleTable;
use Tikivn\Oms\Order\Model\Order;

class ListEventCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:list-event {code}';

    public function exec($code) {
        $repository = $this->app->getService('orderRepository');
        $collectionOrderEvent = $repository->getEvents($code);
        
        self::printTable($collectionOrderEvent);
    }

    public static function printTable($collectionOrderEvent) {
        $table = new ConsoleTable();
        $table->addHeader('event_id')
            ->addHeader('timestamp')
            ->addHeader('action');

        foreach ($collectionOrderEvent as $orderEvent) {
            $table->addRow();
            $table->addColumn($orderEvent->getProperty('request_id'))
                ->addColumn($orderEvent->getProperty('request_time'))
                ->addColumn($orderEvent->getProperty('payload.action'));
        }

        $table->display();
    }
}