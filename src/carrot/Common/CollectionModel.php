<?php
namespace Carrot\Common;

class CollectionModel implements \IteratorAggregate, \Countable
{
    protected $_data = [];

    protected function model() : string
    {
        $className = get_class($this);
        return \str_replace('Collection', '', $className);
        return $className;
    }

    public static function hydrate(array $dataArray) {
        $instance = new static;
        if (!empty($dataArray) && !is_array($dataArray[0])) {
            throw new \InvalidArgumentException();
        }
        foreach ($dataArray as $elem) {
            $modelClass = $instance->model();
            $model = new $modelClass();
            $model->assign($elem);
            $instance->append($model);
            unset($model);
        }
        return $instance;
    }

    public function getIterator() {
        return new \ArrayIterator($this->_data);
    }

    public function append(Model $model) {
        $this->_data[] = $model;
    }

    public function count() {
        return count($this->_data);
    }

    public function join(CollectionModel $collection) : void
    {
        foreach ($collection as $c) {
            $this->append($c);
        }
    }
}