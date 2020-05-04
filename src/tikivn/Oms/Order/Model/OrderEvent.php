<?php
namespace Tikivn\Oms\Order\Model;

class OrderEvent extends \Carrot\Common\Model
{
    public function assign(array $data) {
        $payload = $data['payload'];

        $eventData = [
            'request_id' => $data['request_id'],
            'request_time' => $data['request_time'],
            'source' => $data['source'],
            'action' => $payload['action'],
            'changes' => $payload['changes'],
            'backtrace' => $data['backtrace'],
        ];

        $order = (new Order);
        $order->assign($payload['order']);
        $eventData['order'] =  $order;

        parent::assign($eventData);
    }
}
