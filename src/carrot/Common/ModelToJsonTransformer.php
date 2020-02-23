<?php
namespace Carrot\Common;

class ModelToJsonTransformer
{
    private $arrayTransformer;

    public function __construct(array $configs = []) {
        $this->arrayTransformer = new ModelToArrayTransformer();
        if (!empty($configs['visibleFields']) && is_array($configs['visibleFields'])) {
            $this->arrayTransformer->setVisibleFields($configs['visibleFields']);
        }
    }

    public function setVisibleFields(array $fields) {
        $this->arrayTransformer->setVisibleFields($fields);
    }

    public function transform(Model $model) {
        return \json_encode($this->arrayTransformer->transform($model), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
}