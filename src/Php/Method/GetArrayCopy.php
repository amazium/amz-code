<?php

namespace Amz\Code\Php\Method;

use Amz\Code\Format\Indenter;
use Amz\Code\Php\Imports;
use Amz\Code\Php\Method;
use Amz\Code\Php\NamingStrategy;
use Amz\Code\Php\Properties;
use Amz\Code\Php\Property;
use Amz\Core\Contracts\Helper\SanitizeArrayCopy;

class GetArrayCopy
{
    public static function fromProperties(
        Properties $properties,
        Imports $imports
    ): Method {
        // Add required imports
        $sanitizeClass = $imports->add(SanitizeArrayCopy::class);

        // Create method
        $method = [];
        $method['name']            = 'getArrayCopy';
        $method['description']     = 'Get an array representation of the object';
        $method['return_type']     = 'array';
        $method['can_return_null'] = false;
        $method['parameters']      = [
            [
                'name'         => 'options',
                'type'         => 'array',
                'defaultValue' => [],
                'description'  => 'Array with array copy options',
            ],
        ];

        // Create body lines
        $bodyItems = [];
        /** @var Property $property */
        foreach ($properties as $property) {
            $name = $property->getName();
            $bodyItems[] = sprintf(
                '$return["%s"] => $this->%s(),',
                NamingStrategy::getArrayKey($name),
                NamingStrategy::getterName($name, $property->getType())
            );
        }
        $bodyItemsCode = Indenter::indent(implode(PHP_EOL, $bodyItems), 1);

        // Create body
        $method['body'] = <<<BODY
            // Compose return array
            \$return = [
            {$bodyItemsCode}
            ];
            return {$sanitizeClass}::sanitize(\$return, \$options);
            BODY;

        // Create the method
        return Method::fromArray($method);
    }
}
