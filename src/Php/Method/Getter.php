<?php

namespace Amz\Code\Php\Method;

use Amz\Code\Php\Imports;
use Amz\Code\Php\Method;
use Amz\Code\Php\NamingStrategy;
use Amz\Code\Php\Property;
use Amz\Code\Php\Scope;

class Getter
{
    public static function fromProperty(
        Property $property,
        string $scope = Scope::PUBLIC,
        Imports $imports
    ): Method {
        // Get the property name for us in template and method name
        $name = $property->getName();
        $type = $imports->add($property->getType());

        // Compose method config
        $method = [];
        $method['name']            = NamingStrategy::getterName($name, $type);
        $method['scope']           = $scope;
        $method['return_type']     = $type ?: 'mixed';
        $method['can_return_null'] = $property->isOptional();
        $method['description']     = sprintf("Getter for $%s", $property->getName());

        // Add body
        $method['body'] = <<<BODY
            return \$this->{$name};
            BODY;

        // Create the method
        return Method::fromArray($method);
    }
}
