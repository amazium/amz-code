<?php

namespace Amz\Code\Php\Method;

use Amz\Code\Php\Method;

class Name
{
    public static function fromNameReference(string $reference)
    {
        $method = [];
        $method['name']        = 'name';
        $method['type']        = 'string';
        $method['description'] = 'Name identifying the object';
        $method['body']        = "return {$reference};";
        return Method::fromArray($method);
    }
}
