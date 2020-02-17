<?php
namespace Tikivn\Oms\Order\Model;

class CollectionOrderEvent extends \Carrot\Common\CollectionModel
{
    protected function model() : string
    {
        return OrderEvent::class;
    }

    public function getEvent(string $eventId) : ?OrderEvent
    {
        foreach ($this->_data as $event) {
            if ($event->getRequestId() == $eventId) {
                return $event;
            }
        }
        return null;
    }
}
