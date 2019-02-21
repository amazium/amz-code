<?php

namespace Amz\Code\Php;

use Amz\Core\Object\Collection;

class Methods extends Collection
{
    /**
     * @return string
     */
    public function elementClass(): string
    {
        return Method::class;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $return = '';
        foreach ($this as $method) {
            if ($return !== '') {
                $return.= PHP_EOL . PHP_EOL;
            }
            $return.= (string)$method;
        }
        return $return;
    }
}
