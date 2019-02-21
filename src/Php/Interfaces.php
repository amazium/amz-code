<?php

namespace Amz\Code\Php;

use Amz\Core\Object\ArrayObject;

class Interfaces extends ArrayObject
{
    public function __toString()
    {
        if ($this->count() === 0) {
            return '';
        }
        $interfaces = $this->getArrayCopy();
        sort($interfaces);
        return ' implements ' . implode(', ', $interfaces);
    }
}
