<?php

namespace Amz\Code\Php;

use Amz\Core\Exception\RuntimeException;
use Amz\Core\Object\ArrayObject;

class Imports extends ArrayObject
{

    /**
     * @param string $fullClassName
     * @param string|null $alias
     * @return string
     */
    public function add(string $fullClassName, string $alias = null): string
    {
        // Ignore scalar types
        if (in_array(
            $fullClassName,
            [ 'string', 'int', 'integer', 'bool', 'boolean', 'array', 'float', 'double', 'mixed', 'void' ]
        )) {
            return $fullClassName;
        }
        // Create and return alias
        $this->offsetSet($fullClassName, $alias);
        return strval($this->offsetGet($fullClassName));
    }

    /**
     * @param mixed $fullClassName
     * @param string|null $alias
     */
    public function offsetSet($fullClassName, $alias = null): void
    {
        // We don't want to deal with numeric offsets
        if (is_numeric($alias)) {
            $alias = null;
        } elseif (is_numeric($fullClassName) && is_string($alias)) {
            $fullClassName = $alias;
            $alias = null;
        }

        // Check if we already have the class and return the alias
        if ($this->offsetExists($fullClassName)) {
            return;
        }

        // Create an alias if none is provided
        if (is_null($alias)) {
            $alias = trim(substr($fullClassName, strrpos($fullClassName, '\\')), '\\');
        }

        // Check if the alias is unique
        $aliases = $this->count() > 0 ? array_flip($this->getArrayCopy()): [];
        if (!is_null($alias) && isset($aliases[$alias])) {
            if ($alias === $fullClassName) {
                return;
            }
            // TODO: add proper exception
            throw new RuntimeException(sprintf(
                'Cannot add alias %s for %s, already exists with different class: %s',
                $alias,
                $fullClassName,
                $aliases[$alias]
            ));
        }
        parent::offsetSet($fullClassName, $alias);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            if (strpos($offset, '\\') === false) {
                if (in_array($offset, $this->getArrayCopy())) {
                    return $offset;
                }
            }
        }
        return parent::offsetGet($offset);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $returnLines = [];
        foreach ($this as $fullClassName => $alias) {
            $className = trim(substr($fullClassName, strrpos($fullClassName, '\\')), '\\');
            $returnLines[] = sprintf(
                'use %s%s;',
                $fullClassName,
                $alias !== $className ? ' as ' . $alias : ''
            );
        }
        sort($returnLines);
        return implode(PHP_EOL, $returnLines);
    }
}
