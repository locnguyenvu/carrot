<?php
namespace App\Console\Order;

use Carrot\Common\{Model, CollectionModel};
use LucidFrame\Console\ConsoleTable;
use Tikivn\Oms\Order\Model\{Order, CollectionOrderEvent};

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
        
        $this->printTable($collectionOrderEvent);

        do {
            $command = readline("\n>> ");

            list($action, $param) = explode(' ', $command);
            switch($action) {
                case 'trackField':
                    $trackFields = explode(',', $param);
                    $this->printTrackFields($trackFields, $collectionOrderEvent);
                    break;
                case 'tf':
                    $trackFields = explode(',', $param);
                    $this->printTrackFields($trackFields, $collectionOrderEvent);
                    break;
                case 'exit':
                    break;
                default: 
                    $this->printEventById($action, $collectionOrderEvent);
                    break;
            }
        } while ($command != 'exit');
    }

    protected function printTable($collectionOrderEvent) {
        $table = new ConsoleTable();
        $table->addHeader('event_id')
            ->addHeader('timestamp')
            ->addHeader('action');

        foreach ($collectionOrderEvent as $orderEvent) {
            $table->addRow();
            $table->addColumn($orderEvent->getProperty('request_id'))
                ->addColumn($orderEvent->getProperty('request_time'))
                ->addColumn($orderEvent->getProperty('action'));
        }

        $table->display();
    }

    protected function printEventById(string $uuid, CollectionOrderEvent $collectionOrderEvent) {
        $event = $collectionOrderEvent->getEvent($uuid);
        echo $event->toJson();
    }

    protected function printTrackFields(array $trackFields, CollectionOrderEvent $collectionOrderEvent) {
        echo app('console_color')->apply(['bold'], '=========================== Track fields =========================== ').PHP_EOL;
        foreach ($collectionOrderEvent as $event) {
            echo implode(' | ',[
                app('console_color')->apply(['magenta'], $event->getRequestId()),
                $event->getRequestTime(),
                app('console_color')->apply(['bold', 'light_blue'], $event->getAction())
            ]).PHP_EOL;
            $order = $event->getOrder();
            $transformer = new \Carrot\Common\ModelToArrayTransformer($order);
            echo json_encode($transformer->filterFields($trackFields), JSON_PRETTY_PRINT);
            echo PHP_EOL;
        }
    }
}
