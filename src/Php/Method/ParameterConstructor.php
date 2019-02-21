<?php

namespace Amz\Code\Php\Method;

use Amz\Code\Php\Method;
use Amz\Code\Php\NamingStrategy;
use Amz\Code\Php\Properties;
use Amz\Code\Php\Property;

class ParameterConstructor
{
    public static function fromProperties(
        Properties $properties
    ): Method {
        $method = [];
        $method['name']        = '__constructor';
        $method['description'] = 'Constructor';

        // Add parameters & body parts
        $parameters = $bodyLines = [];
        /** @var Property $property */
        foreach ($properties as $property) {
            $name = $property->getName();
            $isCollection = $property->isCollection();
            $type = $property->getType();
            $param = [
                'name' => $name,
                'type' => $isCollection ? 'array' : $type,
                'can_be_null' => $property->isOptional(),
            ];
            if ($property->isOptional()) {
                $param['default_value'] = $property->getConstructorDefaultValue();
            }
            $parameters[] = $param;

            $setter = NamingStrategy::setterName($name);
            if ($isCollection) {
                $bodyLines[] = "\$this->{$setter}(\${$name} ?? []);";
            } else {
                $bodyLines[] = <<<BODY
                if (!is_null(\${$name})) {
                    \$this->{$setter}(\${$name});
                }
                BODY;
            }

        }
        $method['parameters'] = $parameters;
        $method['body'] = implode(PHP_EOL, $bodyLines);

        return Method::fromArray($method);
    }
}
