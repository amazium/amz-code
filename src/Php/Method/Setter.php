<?php

namespace Amz\Code\Php\Method;

use Amz\Code\Php\Imports;
use Amz\Code\Php\Method;
use Amz\Code\Php\NamingStrategy;
use Amz\Code\Php\Property;
use Amz\Code\Php\Scope;
use Amz\Core\Exception\InvalidArgumentException;

class Setter
{
    public static function fromProperty(
        Property $property,
        string $scope = Scope::PUBLIC,
        Imports $imports
    ): Method {
        // Add required imports
        $invalidArgumentExceptionClass = $imports->add(InvalidArgumentException::class);

        // Get the property name for us in template and method name
        $name = $property->getName();
        $type = $imports->add($property->getType());

        // Compose method config
        $method = [];
        $method['name']        = NamingStrategy::setterName($name);
        $method['scope']       = $scope;
        $method['description'] = sprintf("Setter for $%s", $property->getName());

        // Add parameters
        $paramType = ($type && $property->isCollection()) ? $type . '|array' : $type;
        $method['parameters'] = [
            [
                'name'        => $name,
                'type'        => $paramType,
                'can_be_null' => $property->isOptional(),
            ]
        ];
        $type = $property->getType();

        // Add body
        $body = '';
        if ($property->isCollection() && !is_null($type) && $type !== '') {
            // TODO: Add to imports: InvalidArgumentException
            $body = <<<BODY
                if (is_array(\${$name})) {
                    \${$name} = new {$type}(\${$name});
                }
                if (!\${$name} instanceof {$type}) {
                    throw new {$invalidArgumentExceptionClass}(sprintf(
                        "{$method['name']} expects an array or instance of %s as input, received: %s",
                        {$type}::class,
                        is_object(\${$name}) ? get_class(\${$name}) : gettype(\${$name})
                    ));
                }
                \$this->{$name} = \${$name};
                BODY;
        } else {
            $body .= <<<BODY
                \$this->{$name} = \${$name};
                BODY;
        }
        $method['body'] = $body;

        // Create the method
        return Method::fromArray($method);
    }
}
