<?php

if (!function_exists('array_get')) {
    function array_get(array $source, string $keyStructure, $defaultValue = null)
    {
        return \Carrot\Util\Arry::getByPath($source, $keyStructure, $defaultValue);
    }
}

if (!function_exists('array_generate')) {
    function array_generate(string $keyStructure, $value = null) 
    {
        return \Carrot\Util\Arry::generate($keyStructure, $vaule);
    }
}
if (!function_exists('array_set')) {
    function array_set(&$source, $path, $value) {
        return \Carrot\Util\Arry::setByPath($source, $path, $value);
    }
}

if (!function_exists('string_camelize')) {
    function string_camelize(string $snakeCase) : string {
        $chunks = explode('_', $snakeCase);
        $camelCase = array_shift($chunks);
        foreach ($chunks as $chunk) {
            $camelCase .= ucfirst($chunk);
        }
        return $camelCase;
    }
}

if (!function_exists('string_snakelize')) {
    function string_snakelize($string)
    {
        return strtolower(preg_replace(
            array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'),
            array('\\1_\\2', '\\1_\\2'),
            $string
        ));
    }
}

if (!function_exists('app')) {
    function app(string $service) {
        if ($service == null) {
            return \Carrot\Container::$di;
        } elseif (\Carrot\Container::$di->has($service)) {
            return \Carrot\Container::$di->get($service);
        }
        return \Carrot\Container::$di->create($service);
    }
}