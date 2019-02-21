<?php

namespace Amz\Code\Php;

use Amz\Core\Object\Collection;

class Params extends Collection
{
    /**
     * @return string
     */
    public function elementClass(): string
    {
        return Param::class;
    }

    /**
     * @return $string
     */
    public function __toString(): string
    {
        // If we have no params, return an empty string
        if ($this->count() === 0) {
            return '';
        }
        $params = [];
        foreach ($this as $param) {
            $params[] = strval($param);
        }
        return implode(', ', $params);
    }
}
