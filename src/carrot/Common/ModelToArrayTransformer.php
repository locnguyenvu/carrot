<?php
namespace Carrot\Common;

class ModelToArrayTransformer
{
    protected $model;
    protected $visibleFields = [];

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function setVisibleField(array $fields) : void
    {
        $this->visibleFields = $fields;
    }

    public function transform() : array
    {
        $dataArray = [];
        $modelProperties = $this->model->getAllProperties();

        foreach ($modelProperties as $key => $value) {
            if ($value instanceof CollectionModel) {
                $collection = [];
                foreach ($value as $elem) {
                    $collection[] = (new static($elem))->transform();
                    $dataArray[$key] = $collection;
                }
                continue;
            }
            if ($value instanceof Model) {
                $dataArray[$key] = (new static($value))->transform();
                continue;
            }

            $dataArray[$key] = $value;
        }

        if (!empty($this->visibleFields)) {
            return $this->visibleData($dataArray);
        }

        return $dataArray;
    }

    public function filterFields(array $fields, array $source = []) {
        if (empty($source)) {
            $source = $this->transform();
        }

        if (empty($fields)) return $source;

        $result = [];
        foreach ($fields as $field) {

            if (array_key_exists($field, $source)) {
                $result[$field] = $source[$field];
                continue;
            }

            $result[$field] = array_get($source, $field);

        }
        return $result;
    }
}
