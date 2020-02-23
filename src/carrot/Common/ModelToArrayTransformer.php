<?php
namespace Carrot\Common;

class ModelToArrayTransformer
{
    protected $model;
    protected $visibleFields = [];

    public function __construct(array $configs = []) {
        if (!empty($configs['visibleFields']) && is_array($configs['visibleFields'])) {
            $this->setVisibleFields($configs['visibleFields']);
        }
    }

    public function setVisibleFields(array $fields) : void
    {
        $this->visibleFields = $fields;
    }

    public function transform(Model $model) : array
    {
        $dataArray = [];
        $modelProperties = $model->getAllProperties();

        foreach ($modelProperties as $key => $value) {
            if ($value instanceof CollectionModel) {
                $collection = [];
                foreach ($value as $elem) {
                    $collection[] = (new static())->transform($elem);
                }
                $dataArray[$key] = $collection;
                continue;
            }
            if ($value instanceof Model) {
                $dataArray[$key] = (new static())->transform($value);
                continue;
            }

            $dataArray[$key] = $value;
        }
        if (empty($this->visibleFields)) {
            return $dataArray;
        }
        return $this->filterVisibleFields($dataArray);
    }

    protected function filterVisibleFields(array $source) : array
    {
        $result = [];
        foreach ($this->visibleFields as $field) {

            if (array_key_exists($field, $source)) {
                $result[$field] = $source[$field];
                continue;
            }

            if (\preg_match('/\[\]\./', $field)) {
                list($collectionKey, $collectionItemfield) = explode('[].', $field);
                if (!array_key_exists($collectionKey, $source) || !is_array($source[$collectionKey])) {
                    continue;
                }
                if (!array_key_exists($collectionKey, $result)) {
                    $result[$collectionKey] = [];
                }
                foreach ($source[$collectionKey] as $index => $collectionItem) {
                    $tmp = $result[$collectionKey][$index] ?? [];
                    array_set($tmp, $collectionItemfield, array_get($collectionItem, $collectionItemfield));
                    $result[$collectionKey][$index] = $tmp;
                }
                continue;
            }

            array_set($result, $field, array_get($source, $field));
        }
        return $result;
    }
}
