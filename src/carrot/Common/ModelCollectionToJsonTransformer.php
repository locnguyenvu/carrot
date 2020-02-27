<?php
namespace Carrot\Common;

class ModelCollectionToJsonTransformer
{
    private $modelToArrayTransformer;

    public function __construct(array $configs = [])
    {
        $this->modelToArrayTransformer = new ModelToArrayTransformer();
        if (isset($configs['visibleFields']) && is_array($configs['visibleFields'])) {
            $this->modelToArrayTransformer->setVisibleFields($configs['visibleFields']);
        }
    }

    public function setVisibleFields(array $fields) : void
    {
        $this->modelToArrayTransformer->setVisibleFields($fields);
    }

    public function transform(ModelCollection $collection) : string
    {
        $result = [];
        foreach ($collection as $elem) {
            $result[] = $this->modelToArrayTransformer->transform($elem);
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
}