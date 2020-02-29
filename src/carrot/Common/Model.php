<?php
namespace Carrot\Common;

class Model
{
    protected $_properties = [];

    public function assign(array $data) {
        $this->_properties = $data;
    }

    public function __call($name, $args) 
    {
        if (preg_match('/get.*/', $name)) {
            $proteryName = string_snakelize(str_replace('get', '', $name));
            return $this->getProperty($proteryName);
        }
        if (\method_exists($this, $name)) {
            \call_user_func_array([$this, $name], $args);
        }
        throw new \BadMethodCallException();
    }

    public function getProperty($key) {
        return array_get($this->_properties, $key);
    }

    public function getAllProperties() : array
    {
        return $this->_properties;
    }
}
