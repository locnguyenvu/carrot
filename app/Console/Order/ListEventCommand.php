<?php
namespace App\Console\Order;

use Carrot\Common\{Model, ModelCollection, ModelToArrayTransformer, ModelToJsonTransformer};
use Carrot\Util\Cjson;
use LucidFrame\Console\ConsoleTable;
use Tikivn\Oms\Order\Model\{Order, OrderEventCollection};

class ListEventCommand extends \Carrot\Console\Command
{
    protected static $pattern = 'order:list-event {code}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($code) {
        $OrderEventCollection = $this->orderRepository->getEvents($code);

        $trackFields = $this->hasOption('trackFields') ? explode(',', $this->getOption('trackFields')) : [];
        if (!empty($trackFields) && !$this->hasOption('detail')) {
            return $this->printTrackFields($trackFields, $OrderEventCollection);
        }
        
        $this->printOverviewTable($OrderEventCollection);

        if (!$this->hasOption('detail')) {
            return;
        }

        do {
            $command = readline("\n>> ");
            list($action, $param) = explode(' ', $command.' ');

            $alias = [
                'q' => 'exit',
                'ls' => 'list',
            ];
            if (array_key_exists($action, $alias)) {
                $action = $alias[$action];
            }
            switch($action) {
                case 'exit':
                    break;
                case 'list':
                    $this->printOverviewTable($OrderEventCollection);
                    break;
                case 'trackFields':
                    if ($param == '*') {
                        $trackFields = [];
                    } else {
                        $trackFields = explode(',', $param);
                    }
                    break;
                default: 
                    $this->printEventById($action, $OrderEventCollection, $trackFields);
                    break;
            }
        } while ($action != 'exit');
    }

    protected function printOverviewTable($OrderEventCollection) {
        $table = new ConsoleTable();
        $table->addHeader('event_id')
            ->addHeader('timestamp')
            ->addHeader('action');

        foreach ($OrderEventCollection as $orderEvent) {
            $table->addRow();
            $table->addColumn($orderEvent->getProperty('request_id'))
                ->addColumn($orderEvent->getProperty('request_time'))
                ->addColumn($orderEvent->getProperty('action'));
        }

        $table->display();
        printf("\nTotal %d events", count($OrderEventCollection));
    }

    protected function printEventById(string $uuid, OrderEventCollection $OrderEventCollection, array $filterFields = []) {
        $event = $OrderEventCollection->getEvent($uuid);
        $transformer = new ModelToJsonTransformer();
        $order = $event->getOrder();
        if (!empty($filterFields)) {
            $transformer->setVisibleFields($filterFields);
        }
        Cjson::printWithColor($transformer->transform($order));
    }

    protected function printTrackFields(array $trackFields, OrderEventCollection $OrderEventCollection) {
        echo app('console_color')->apply(['bold'], '=========================== Track fields =========================== ').PHP_EOL;
        foreach ($OrderEventCollection as $event) {
            echo implode(' | ',[
                app('console_color')->apply(['magenta'], $event->getRequestId()),
                $event->getRequestTime(),
                app('console_color')->apply(['bold', 'light_blue'], $event->getAction())
            ]).PHP_EOL;
            $order = $event->getOrder();
            $transformer = new \Carrot\Common\ModelToJsonTransformer();
            $transformer->setVisibleFields($trackFields);
            Cjson::printWithColor($transformer->transform($order));
            print("\n");
        }
        printf("\nTotal %d events", count($OrderEventCollection));
    }
}
