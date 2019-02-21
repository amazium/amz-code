<?php

namespace Amz\Code\Php;

use Amz\Core\Object\ArrayObject;

class Parents extends ArrayObject
{
    public function __toString()
    {
        if ($this->count() === 0) {
            return '';
        }
        $parents = $this->getArrayCopy();
        sort($parents);
        return ' extends ' . implode(', ', $parents);
    }
}
