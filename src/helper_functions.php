<?php

if (!function_exists('array_get')) {
    function array_get(array $source, string $keyStructure, $defaultValue = null)
    {
        $tree = explode('.', $keyStructure);
        $lastFoundValue = $source;
        foreach ($tree as $node) {
            if (!is_array($lastFoundValue)) {
                return $lastFoundValue;
            }
            if (!isset($lastFoundValue[$node])) {
                return $defaultValue;
            }
            $lastFoundValue = $lastFoundValue[$node];
        }
        return $lastFoundValue;
    }
}

if (!function_exists('carrot')) {
    function carrot($service = null) {
        if ($service == null) {
            return \App\WebApp::$di;
        } elseif (\App\WebApp::$di->has($service)) {
            return \App\WebApp::$di->get($service);
        }
        return \App\WebApp::$di->create($service);
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