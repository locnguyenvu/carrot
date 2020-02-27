<?php
namespace Carrot\Util;

class Cjson
{
    public const TYPE_NUMBER = 'number';
    public const TYPE_STRING = 'string';
    public const TYPE_OBJECT = 'object';
    public const TYPE_ARRAY = 'array';
    public const TYPE_BOOLEAN = 'boolean';

    public const INDENT_SIZE = '    ';


    public static function printPrettyWithColor(string $jsonString, int $depth = 0) : void
    {
        if (self::isCompressedJsonString($jsonString)) {
            $content = static::getContent($jsonString);
            if (static::valueType($jsonString) == static::TYPE_ARRAY) {
                static::printCompressedArray($jsonString, $depth);
                return;
            }
    
            static::printCompressedObject($jsonString, $depth);
            return;
        }

    }

    protected static function printCompressedArray(string $arrayStr, int $depth) {
        $contentIndent = implode('', array_fill(0, $depth+1, self::INDENT_SIZE));
        $bracketIndent = implode('', array_fill(0, $depth, self::INDENT_SIZE));
        if ($depth === 0) {
            printf("[\n");
        }
        $content = static::getContent($arrayStr);
        if (\preg_match('/^\d+(,\d+)*/', $content)) {
            $numbers = explode(',', $content);
            foreach ($numbers as $index => $num) {
                printf($contentIndent);
                self::printNumberWithColor($num);
                if ($index < count($numbers)-1) {
                    echo ',';
                }
                print("\n");
            }
        }
        if (\preg_match('/^"[A-Za-z0-9_"]+("\s*,\s*[A-Za-z0-9_"])*/', $content)) {
            $content = preg_replace('/"\s*,\s*"/', '"|"', $content);
            $strings = explode('|', $content);
            foreach ($strings as $index => $string) {
                printf($contentIndent);
                self::printStringWithColor($string);
                if ($index < count($strings)-1) {
                    echo ',';
                }
                print("\n");
            }
        }
        if (\preg_match('/\{.*\},*(\{.*\})*/', $content)) {
            $content = preg_replace('/\}\s*,\s*\{/', '}|{', $content);
            $arrayElem = explode('|', $content);
            foreach ($arrayElem as $index => $arrayE) {
                static::printPrettyWithColor($arrayE, $depth+1);
                if ($index < count($arrayElem)-1) {
                    echo ',';
                }
                print("\n");
            }
        }
        print($bracketIndent."]");
    }

    protected static function printCompressedObject(string $objectStr, int $depth) {
        $content = static::getContent($objectStr);
        $cursor = 0;
        $elements = [];
        $elementStr = '';
        $isObjectStart = false;
        $isObjectEnd = false;
        $isArrayStart = false;
        $isArrayEnd = false;

        do {
            $char = substr($content, $cursor, 1);
            if ($char === ',' && ($isArrayStart === $isArrayEnd) && ($isObjectStart == $isObjectEnd)) {
                $elements[] = $elementStr;
                $elementStr = '';
                $isObjectStart = false;
                $isObjectEnd = false;
                $isArrayStart = false;
                $isArrayEnd = false;
                $cursor++;
                continue;
            }
            if ($cursor+1 == strlen($content)) {
                $elementStr.=$char;
                $elements[] = $elementStr;
                $cursor++;
                continue;
            }
    
            if ($char === '{') {
                $isObjectStart = true;
            } else if ($char === '[') {
                $isArrayStart = true;
            } else if ($char === '}') {
                $isObjectEnd = true;
            } else if ($char === ']') {
                $isArrayEnd = true;
            }
    
            $elementStr.=$char;
            $cursor++;
    
        } while ($cursor < strlen($content));

        $elements = array_map('trim', $elements);

        $contentIndent = implode('', array_fill(0, $depth+1, self::INDENT_SIZE));
        if ($depth < 1) {
            $bracketIndent = '';
        } else {
            $bracketIndent = implode('', array_fill(0, $depth, self::INDENT_SIZE));
        }
        print($bracketIndent."{\n");
        foreach ($elements as $key => $elem) 
        {
            list($key, $value) = static::explodeElement($elem);
            printf($contentIndent);
            self::printKeyWithColor($key);
            switch(static::valueType($value)) {
                case static::TYPE_STRING:
                    self::printStringWithColor($value);
                    if ($key < count($elements)-1) { print(','); }
                    break;
                case static::TYPE_NUMBER:
                    self::printNumberWithColor($value);
                    if ($key < count($elements)-1) { print(','); }
                    break;
                case static::TYPE_BOOLEAN:
                    self::printBooleanWithColor($value);
                    break;
                case static::TYPE_OBJECT:
                    print("{\n");
                    self::printPrettyWithColor($value, $depth+1);
                    break;
                case static::TYPE_ARRAY:
                    print("[\n");
                    self::printPrettyWithColor($value, $depth+1);
                    break;
            }
            print("\n");
        }
        print($bracketIndent."}");
    }

    public static function printWithColor(string $jsonString)
    {
        $lines = explode("\n", $jsonString);
        foreach ($lines as $index => $line) {
            $colonPos = strpos($line, ':');
            if ($colonPos === false) {
                print($line);
                print("\n");
                continue;
            }
            $key = substr($line, 0, $colonPos);
            static::printKeyWithColor($key);
            
            $value = substr($line, $colonPos+1, strlen($line));
            if (substr($value, -1) === ',') {
                $value = \substr($value, 0, strlen($value) -1);
            }
            $value = trim($value);

            switch(static::valueType(trim($value))) {
                case static::TYPE_STRING:
                    self::printStringWithColor($value);
                    break;
                case static::TYPE_NUMBER:
                    self::printNumberWithColor($value);
                    break;
                case static::TYPE_BOOLEAN:
                    self::printBooleanWithColor($value);
                    break;
                default:
                    print($value);
            }
            print("\n");
        }
    }

    public static function isCompressedJsonString(string $input) : bool
    {
        return preg_match('/(\[|\{).*(\]|\})/', $input);
    }

    public static function getContent(string $jsonString) {
        return substr($jsonString, 1, (strlen($jsonString) -2));
    }

    public static function explodeElement(string $input) {
        $firstColonPos = strpos($input, ':');

        $key = trim(substr($input, 0, $firstColonPos));
        $value = trim(substr($input, $firstColonPos+1, strlen($input)));

        return [$key, $value];
    }

    protected static function valueType(string $input) {
        if (is_numeric($input)) {
            return static::TYPE_NUMBER;
        }
        if (preg_match('/^\[.*\]$/', $input)) {
            return static::TYPE_ARRAY;
        }
        if (preg_match('/^\{.*\}$/', $input)) {
            return static::TYPE_OBJECT;
        }
        if (preg_match('/^".*/', $input)) {
            return static::TYPE_STRING;
        }
        if (in_array($input, ['true', 'false'])) {
            return static::TYPE_BOOLEAN;
        }
        return null;
    }

    protected static function printKeyWithColor(string $key) {
        printf("\e[1;36m%s: \e[0m", $key);
    }

    protected static function printNumberWithColor(string $number) {
        printf("\e[0;34m%s\e[0m", $number);
    }

    protected static function printStringWithColor(string $string) {
        printf("\e[1;33m%s\e[0m", $string);
    }

    protected static function printBooleanWithColor(string $boolean) {
        printf("\e[0;35m%s\e[0m", $boolean);
    }
}