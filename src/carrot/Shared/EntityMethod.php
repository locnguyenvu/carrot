<?php
namespace Carrot\Shared;

trait EntityMethod
{
    public function hydrate(array $params) : void
    {
        foreach ($params as $key => $value) {
            $objPropKey = string_camelize($key);
            if (property_exists($this, $objPropKey)) {
                $this->$objPropKey = $value;
            }
        }
    }
}