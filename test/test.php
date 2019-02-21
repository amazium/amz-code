<?php

namespace Amz;

use Amz\Code\Php\Cls;
use Amz\Core\Contracts\CreatableFromArray;
use Amz\Core\Contracts\Extractable;
use Amz\Core\Contracts\Named;

require_once __DIR__ . '/../vendor/autoload.php';

$class = [
    'name' => 'Param',
    'features' => [
        'has_parameter_constructor' => true,
        'has_protected_setter_scope' => true,
    ],
    'namespace' => 'Amz\Code',
    'interfaces' => [
        CreatableFromArray::class,
        Named::class,
        Extractable::class,
    ],
    'description' => 'Generated parameter class for testing',
    'type' => Cls::TYPE_CLASS,
    'properties' => [
        [
            'name' => 'name',
            'type' => 'string',
            'description' => 'The name of the parameter, used in the method descriptor (will be sanitized)',
            'is_optional' => false,
        ],
        [
            'name' => 'type',
            'type' => 'string',
            'description' => 'The parameter type to be used, if not provided "mixed" will be assumed',
            'default_value' => 'mixed',
            'is_optional' => false,
        ],
        [
            'name' => 'canBeNull',
            'type' => 'bool',
            'description' => 'Is a null value allowed? Defaults to false',
            'default_value' => false,
            'is_optional' => true,
            'constructor_default_value' => true,
        ],
        [
            'name' => 'defaultValue',
            'description' => 'Default value, makes the parameter optional',
            'is_optional' => true,
        ],
        [
            'name' => 'description',
            'description' => 'Description of the parameter',
            'type' => 'string',
            'is_optional' => true,
        ],
    ],
    'methods' => [
        [
            'name' => '__toString',
            'returnType' => 'string',
            'description' => 'Get the code to write out for a param',
            'body' => <<<BODY
                // Process the type, optional if needed
                \$type = \$this->getType() ?? 'mixed';
                if (\$type != 'mixed') {
                    if (\$this->canBeNull()) {
                        \$type = '?' . \$type;
                    }
                    \$type = \$type . ' ';
                } else {
                    \$type = '';
                }

                // Add the variable
                \$name = \$this->getName();

                // Add the defaultValue if needed
                \$default = \$this->getDefaultValue();
                if (!is_null(\$default)) {
                    if (\$default === ':null:') {
                        \$default = 'null';
                    } elseif (is_array(\$default)) {
                        \$default = Arr::export(\$default, true);
                    } else {
                        \$default = var_export(\$default, true);
                    }
                    \$default = ' = ' . \$default;
                } else {
                    \$default = '';
                }

                // return the string
                return <<<CODE
                    {\$type}\\\${\$name}{\$default}
                    CODE;
                BODY,
        ]
    ]
];

$class = Cls::fromArray($class);
echo $class;

//use Amz\Code\Template\Functions\Php\Export;
//use Amz\Code\Template\Glob\Naming;
//use Amz\Code\Template\Glob\Php;
//use Illuminate\Support\Str;
//use Twig_Loader_Filesystem;
//use Twig_Environment;
//use Twig_Filter;
//
//$extensions = [];
//$filters = [
//];
//
//$loader = new Twig_Loader_Filesystem([
//    __DIR__ . '/../templates'
//]);
//
//$twig = new Twig_Environment(
//    $loader,
//    [
//        'autoescape' => false,
//        'strict_variables' => true,
//        'debug' => true,
//    ]
//);
//
//$twig->addGlobal('naming', new Naming());
//$twig->addGlobal('php', new Php());
//
//foreach ($extensions as $extension) {
//    $twigExtension = new $extension($loader);
//    $twig->addExtension($twigExtension);
//}
//
//foreach ($filters as $name => $callable) {
//    $twig->addFilter(new Twig_Filter($name, $callable));
//}
//
//$template = $twig->loadTemplate('php/command/command.twig');
//echo $template->render(
//    [
//    'namespace' => 'Amz\\Core',
//    'class' => [
//        'name' => 'MyTest',
//        'properties' => ,
////        'methods' => [
////            [
////                'description' => 'I want to do something',
////                'name' => 'doSomething',
////                'scope' => 'public',
////                'isStatic' => false,
////                'isFinal' => false,
////                'isAbstract' => false,
////                'returnType' => 'string',
////                'canReturnNull' => true,
////                'params' => [
////                    [
////                        'name' => 'p1',
////                        'type' => 'string',
////                        'defaultValue' => '123',
////                        'description' => 'P1 something'
////                    ],
////                    [
////                        'name' => 'p2',
////                        'type' => 'int'
////                    ]
////                ]
////            ]
////        ],
//    ],
//]);