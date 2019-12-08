<?php
namespace Carrot\Shared;

class Collection implements \IteratorAggregate, \Countable
{
    protected $_data = [];

    public function getIterator() {
        return $this->_data;
    }

    public function append($value) {
        $this->_data[] = $value;
    }

    public function count() {
        return count($this->_data);
    }

    public function dump() : void
    {
        foreach ($this->_data as $elem) {
            dump($elem);
        }
    }

    public function getFirst() {
        if (empty($this->_data)) {
            return null;
        }
        return $this->_data[0];
    }
}