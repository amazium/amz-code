<?php

namespace Amz\Code\Php;

use Amz\Core\Support\Util\Str;

class NamingStrategy
{
    /**
     * @param string $namespace
     * @return string
     */
    public static function getNamespace(string $namespace): string
    {
        $sanitized = str_replace([ '.', '_', '\\', '-', '|', '/' ], ' ', $namespace);
        $parts = array_map(
            function ($part) {
                return Str::studly($part);
            },
            explode(' ', $sanitized)
        );
        return implode('\\', $parts);
    }

    /**
     * @param string $className
     * @return string
     */
    public static function getClassName(string $className): string
    {
        return Str::studly($className);
    }

    /**
     * @param string $property
     * @return string
     */
    public static function getPropertyName(string $property): string
    {
        return Str::camel($property);
    }

    /**
     * @param string $method
     * @return string
     */
    public static function getMethodName(string $method): string
    {
        if (Str::startsWith($method, '__')) {
            return $method;
        }
        return Str::camel($method);
    }

    /**
     * @param string $param
     * @return string
     */
    public static function getParamName(string $param): string
    {
        return Str::camel($param);
    }


    /**
     * @param string $constant
     * @return string
     */
    public static function getConstant(string $constant): string
    {
        return strtoupper(Str::snake($constant));
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getArrayKey(string $key): string
    {
        return Str::snake($key);
    }

    /**
     * @param string $property
     * @param string $type
     * @return string
     */
    public static function getterName(string $property, string $type): string
    {
        if ($type === 'bool' && Str::startsWith($property, [ 'is', 'has', 'can' ])) {
            return $property;
        }
        return 'get' . ucfirst($property);
    }

    /**
     * @param string $property
     * @return string
     */
    public static function setterName(string $property): string
    {
        return 'set' . ucfirst($property);
    }
}
