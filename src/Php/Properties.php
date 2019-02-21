<?php

namespace Amz\Code\Php;

use Amz\Core\Object\Collection;

class Properties extends Collection
{
    /**
     * @return string
     */
    public function elementClass(): string
    {
        return Property::class;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $return = '';
        foreach ($this as $property) {
            if ($return !== '') {
                $return.= PHP_EOL . PHP_EOL;
            }
            $return.= (string)$property;
        }
        return $return;
    }
}
