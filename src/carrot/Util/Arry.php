<?php
namespace Carrot\Util;

class Arry
{
    public static function getByPath(array $source, string $path, $defaultValue = null) 
    {
        $levelDepth = explode('.', $path);
        $lastFoundValue = $source;
        foreach ($levelDepth as $node) {
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

    public static function generate(string $pathStructure, $value = null) {
        $dotPos = strpos($pathStructure, '.');
        if ($dotPos > 0) {
            return [substr($pathStructure, 0, $dotPos) => static::generate(substr($pathStructure, $dotPos + 1), $value)];
        }
        else return [$pathStructure => $value];
    }

    public static function setByPath(array &$source, string $pathStructure, $value) {
        $keys = explode('.', $pathStructure);
        $tracker = &$source;

        do {
            $key = array_shift($keys);
            if (!array_key_exists($key, $tracker ?? [])) {
                $tracker[$key] = static::generate(implode('.', $keys), $value);
            }
            $tracker = &$tracker[$key];
        } while(count($keys) >= 1);

        if ($tracker !== $value) {
            $tracker = $value;
        }
    }
}