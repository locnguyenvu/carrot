<?php
namespace Tikivn\Oms\Order\Model;

class OrderEvent extends \Carrot\Common\Model
{
    public function assign(array $data) {
        $payload = $data['payload'];

        $data['action'] = $payload['action'];
        unset($data['payload']['action']);

        $order = (new Order);
        $order->assign($payload['order']);
        $data['order'] = $order;
        unset($data['payload']['order']);

        $data['changes'] = $payload['changes'];
        unset($data['payload']['changes']);

        parent::assign($data);
    }
}
