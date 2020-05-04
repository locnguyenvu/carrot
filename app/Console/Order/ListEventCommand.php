<?php
namespace App\Console\Order;

use Carrot\Common\{Model, ModelCollection, ModelToArrayTransformer, ModelToJsonTransformer};
use Carrot\Util\Cjson;
use LucidFrame\Console\ConsoleTable;
use Tikivn\Oms\Order\Model\{Order, OrderEventCollection};

class ListEventCommand extends \Carrot\Console\Command
{
    const SUBCOMMAND_EXIT = 'exit';
    const SUBCOMMAND_LIST = 'list';
    const SUBCOMMAND_FILTERFIELDS = 'filterFields';
    const SUBCOMMAND_BACKTRACE = 'backtrace';

    public static $subCommands = [
        self::SUBCOMMAND_EXIT,
        self::SUBCOMMAND_LIST,
        self::SUBCOMMAND_FILTERFIELDS,
        self::SUBCOMMAND_BACKTRACE,
    ];


    protected static $pattern = 'order:list-event {code}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($code) {
        $orderEventCollection = $this->orderRepository->getEvents($code);

        $filterFields = $this->hasOption('filterFields') ? explode(',', $this->getOption('filterFields')) : [];
        if (!empty($filterFields) && !$this->hasOption('detail')) {
            return $this->printFilterFields($filterFields, $orderEventCollection);
        }
        
        $this->printOverviewTable($orderEventCollection);

        if (!$this->hasOption('detail')) {
            return;
        }

        do {
            $command = readline("\n>> ");
            $uuid = null;
            list($action, $param) = explode(' ', $command.' ');

            $alias = [
                'q' => static::SUBCOMMAND_EXIT,
                'ls' => static::SUBCOMMAND_LIST,
                'ff' => static::SUBCOMMAND_FILTERFIELDS,
                'bt' => static::SUBCOMMAND_BACKTRACE,
            ];
            if (array_key_exists($action, $alias)) {
                $action = $alias[$action];
            }
            if (!in_array($action, static::$subCommands)) { $uuid = $action; }
            switch($action) {
                case 'exit':
                    break;
                case 'list':
                    $this->printOverviewTable($orderEventCollection);
                    break;
                case 'filterFields':
                    if ($param == '*') {
                        $filterFields = [];
                    } else {
                        $filterFields = explode(',', $param);
                    }
                    break;
                case 'backtrace':
                    $uuid = $param;
                    $this->printBacktrace($uuid, $orderEventCollection);
                    break;
                default:
                    $this->printEventById($uuid, $orderEventCollection, $filterFields);
                    break;
            }
        } while ($action != 'exit');
    }

    protected function printOverviewTable($orderEventCollection) {
        $table = new ConsoleTable();
        $table->addHeader('event_id')
            ->addHeader('timestamp')
            ->addHeader('action');

        foreach ($orderEventCollection as $orderEvent) {
            $table->addRow();
            $table->addColumn($orderEvent->getProperty('request_id'))
                ->addColumn($orderEvent->getProperty('request_time'))
                ->addColumn($orderEvent->getProperty('action'));
        }

        $table->display();
        printf("\nTotal %d events", count($orderEventCollection));
    }

    protected function printEventById(string $uuid, OrderEventCollection $orderEventCollection, array $filterFields = []) {
        $event = $orderEventCollection->getEvent($uuid);
        $transformer = new ModelToJsonTransformer();
        $order = $event->getOrder();
        if (!empty($filterFields)) {
            $transformer->setVisibleFields($filterFields);
        }
        Cjson::printWithColor($transformer->transform($order));
    }

    protected function printFilterFields(array $filterFields, OrderEventCollection $orderEventCollection) {
        echo app('console_color')->apply(['bold'], '=========================== Track fields =========================== ').PHP_EOL;
        foreach ($orderEventCollection as $event) {
            echo implode(' | ',[
                app('console_color')->apply(['magenta'], $event->getRequestId()),
                $event->getRequestTime(),
                app('console_color')->apply(['bold', 'light_blue'], $event->getAction())
            ]).PHP_EOL;
            $order = $event->getOrder();
            $transformer = new \Carrot\Common\ModelToJsonTransformer();
            $transformer->setVisibleFields($filterFields);
            Cjson::printWithColor($transformer->transform($order));
            print("\n");
        }
        printf("\nTotal %d events", count($orderEventCollection));
    }

    protected function printBackTrace(string $uuid, OrderEventCollection $orderEventCollection) : void {
        $event = $orderEventCollection->getEvent($uuid);
        $backTrace = $event->getBacktrace();
        dump($backTrace);
    }
}
