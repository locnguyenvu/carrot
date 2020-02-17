<?php
namespace App\Console\Order;

use LucidFrame\Console\ConsoleTable;
use Tikivn\Oms\Order\Model\Order;

class ListEventCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:list-event {code}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($code) {
        $collectionOrderEvent = $this->orderRepository->getEvents($code);
        
        self::printTable($collectionOrderEvent);

        do {
            $command = readline("\nEventID> ");
            $event = $collectionOrderEvent->getEvent($command);
            if (is_null($event)) {
                return;
            }
            echo $event->toJson().PHP_EOL;
            self::printTable($collectionOrderEvent);
        } while ($command != 'exit');
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
