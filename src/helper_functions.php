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

if (!function_exists('array_generate')) {
    function array_generate(string $keyStructure, $value = null) 
    {
        $dotPos = strpos($keyStructure, '.');
        if ($dotPos > 0) {
            return [substr($keyStructure, 0, $dotPos) => array_generate(substr($keyStructure, $dotPos + 1), $value)];
        }
        else return [$keyStructure => $value];
    }
}
if (!function_exists('array_set')) {
    function array_set(&$source, $path, $value) {
        $keys = explode('.', $path);
        $tracker = &$source;

        do {
            $key = array_shift($keys);
            if (!array_key_exists($key, $tracker ?? [])) {
                $tracker[$key] = array_generate(implode('.', $keys), $value);
            }
            $tracker = &$tracker[$key];
        } while(count($keys) >= 1);

        if ($tracker !== $value) {
            $tracker = $value;
        }
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