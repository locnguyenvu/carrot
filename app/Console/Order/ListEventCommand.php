<?php
namespace App\Console\Order;

use Carrot\Common\{Model, CollectionModel, ModelToArrayTransformer, ModelToJsonTransformer};
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

        if ($this->hasOption('trackFields')) {
            $trackFields = explode(',', $this->getOption('trackFields'));
            return $this->printTrackFields($trackFields, $collectionOrderEvent);
        }
        
        $this->printOverviewTable($collectionOrderEvent);

        if (!$this->hasOption('detail')) {
            return;
        }
        do {
            $command = readline("\n>> ");
            list($action, $param) = explode(' ', $command);
            switch($action) {
                case 'exit':
                    break;
                case 'list':
                    $this->printOverviewTable($collectionOrderEvent);
                    break;
                default: 
                    $filterFields = empty($param) ? [] : explode(',', $param);
                    $this->printEventById($action, $collectionOrderEvent, $filterFields);
                    break;
            }
        } while ($command != 'exit');
    }

    protected function printOverviewTable($collectionOrderEvent) {
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

    protected function printEventById(string $uuid, CollectionOrderEvent $collectionOrderEvent, array $filterFields = []) {
        $event = $collectionOrderEvent->getEvent($uuid);
        $transformer = new ModelToJsonTransformer();
        $order = $event->getOrder();
        if (!empty($filterFields)) {
            $transformer->setVisibleFields($filterFields);
        }
        echo $transformer->transform($order);
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
            $transformer = new \Carrot\Common\ModelToJsonTransformer();
            $transformer->setVisibleFields($trackFields);
            echo $transformer->transform($order).PHP_EOL;
        }
    }
}
