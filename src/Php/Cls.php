<?php

namespace Amz\Code\Php;

use Amz\Code\Format\Indenter;
use Amz\Code\Php\Method\ArrayConstructor;
use Amz\Code\Php\Method\GetArrayCopy;
use Amz\Code\Php\Method\Getter;
use Amz\Code\Php\Method\Name;
use Amz\Code\Php\Method\ParameterConstructor;
use Amz\Code\Php\Method\Setter;
use Amz\Core\Contracts\CreatableFromArray;
use Amz\Core\Contracts\Extractable;
use Amz\Core\Contracts\Hydratable;
use Amz\Core\Contracts\Named;
use Amz\Core\Exception\InvalidArgumentException;
use Amz\Core\Support\Util\Str;

class Cls implements Extractable, Named, CreatableFromArray
{
    const TYPE_CLASS = 'class';
    const TYPE_INTERFACE = 'interface';
    const TYPE_TRAIT = 'trait';

    /**
     * The class name
     *
     * @var string
     */
    public $name;

    /**
     * The namespace
     *
     * @var string
     */
    public $namespace;

    /**
     * Description of what the class does
     *
     * @var string
     */
    public $description;

    /**
     * Are we an interface, class or trait
     *
     * @var string
     */
    public $type = self::TYPE_CLASS;

    /**
     * Class' parents
     *
     * @var Parents
     */
    public $parents;

    /**
     * Class' interfaces
     *
     * @var Interfaces
     */
    public $interfaces;

    /**
     * Class' properties
     *
     * @var Properties
     */
    public $properties;

    /**
     * Class' methods
     *
     * @var Methods
     */
    public $methods;

    /**
     * Import statements
     *
     * @var Imports
     */
    public $imports;

    /**
     * Allow the class to have abstract methods
     *
     * @var bool
     */
    public $isAbstract = false;

    /**
     * Prevent the class from being extended
     *
     * @var bool
     */
    public $isFinal = false;

    /**
     * @var array
     */
    public $features = [

    ];

    public function __construct(
        string $name,
        string $namespace,
        string $description,
        $parents = [],
        $interfaces = [],
        $properties = [],
        $methods = [],
        $imports = [],
        bool $isAbstract = false,
        bool $isFinal = false,
        string $type = self::TYPE_CLASS,
        array $features = []
    ) {
        $this->imports = new Imports();
        $this->setType($type);
        $this->setName(Str::studly($name));
        $this->setNamespace($namespace);
        $this->setDescription($description);
        $this->setParents($parents ?? []);
        $this->setInterfaces($interfaces ?? []);
        $this->setImports($imports ?? []);
        $this->setProperties($properties ?? []);
        $this->setMethods($methods ?? []);
        $this->setIsFinal($isFinal); // can be overwritten by isAbstract
        $this->setIsAbstract($isAbstract); // can be overwritten by abstract methods
        $this->setFeatures($features);
        $this->expand();
    }

    /**
     *
     */
    public function expand()
    {
        // Extends based on the properties
        $this->expandProperties();

        // Extend based on the interfaces
        $this->expandInterfaces();

        // Extend based on the interfaces
        $this->expandFeatures();
    }

    /**
     * @param Properties $properties
     */
    public function expandProperties(): void
    {
        /** @var Property $property */
        foreach ($this->getProperties() as $property) {
            // Do getters & setters
            if (!$this->getFeatures()->hasNoGetter()) {
                $this->getMethods()->append(
                    Getter::fromProperty(
                        $property,
                        $this->getFeatures()->getGetterScope(),
                        $this->getImports()
                    )
                );
            }
            if (!$this->getFeatures()->hasNoSetter()) {
                $this->getMethods()->append(
                    Setter::fromProperty(
                        $property,
                        $this->getFeatures()->getSetterScope(),
                        $this->getImports()
                    )
                );
            }
        }
    }

    /**
     *
     */
    public function expandInterfaces(): void
    {
        $interfaces = $this->interfaces->keys();
        if (in_array(Named::class, $interfaces)) {
            if ($this->getProperties()->offsetExists('name')) {
                $this->getMethods()->prepend(Name::fromNameReference('$this->name'));
            } else {
                $this->setIsAbstract(true);
            }
        }
        if (in_array(Hydratable::class, $interfaces)) {

        }
        if (in_array(Extractable::class, $interfaces)) {
            $this->getMethods()->prepend(GetArrayCopy::fromProperties($this->getProperties(), $this->getImports()));
        }
    }

    public function expandFeatures()
    {
        if ($this->getFeatures()->hasParameterConstructor()) {
            $this->getMethods()->prepend(ParameterConstructor::fromProperties($this->getProperties()));
        } elseif ($this->getFeatures()->hasArrayConstructor()) {
            $this->getMethods()->prepend(ArrayConstructor::fromProperties($this->getProperties()));
        }
    }

    /**
     * @param array $payload
     * @return Cls|mixed
     */
    public static function fromArray(array $payload)
    {
        return new static(
            $payload['name'] ?? null,
            $payload['namespace'] ?? null,
            $payload['description'] ?? null,
            $payload['parents'] ?? [],
            $payload['interfaces'] ?? [],
            $payload['properties'] ?? [],
            $payload['methods'] ?? [],
            $payload['imports'] ?? [],
            $payload['is_abstract'] ?? false,
            $payload['is_final'] ?? false,
            $payload['type'] ?? self::TYPE_CLASS,
            $payload['features'] ?? [],
        );
    }


    /**
     * @param array $options
     * @return array
     */
    public function getArrayCopy(array $options = []): array
    {
        return [
            'name' => $this->getName(),
            'namespace' => $this->getNamespace(),
            'type' => $this->getType(),
            'description' => $this->getDescription(),
            'parents' => $this->getProperties()->getArrayCopy($options),
            'interfaces' => $this->getProperties()->getArrayCopy($options),
            'properties' => $this->getProperties()->getArrayCopy($options),
            'methods' => $this->getMethods()->getArrayCopy($options),
            'imports' => $this->getImports()->getArrayCopy($options),
            'is_abstract' => $this->isAbstract(),
            'is_final' => $this->isFinal(),
            'features' => $this->getFeatures()->getArrayCopy($options),
        ];
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Parents
     */
    public function getParents(): Parents
    {
        return $this->parents;
    }

    /**
     * @param array $parents
     */
    public function setParents(array $parents): void
    {
        $this->parents = new Parents();
        foreach ($parents as $parent) {
            $fullClassName = $parent;
            $alias = $this->getImports()->add($parent);
            $this->parents[$fullClassName] = $alias;
        }
    }

    /**
     * @return Interfaces
     */
    public function getInterfaces(): Interfaces
    {
        return $this->interfaces;
    }

    /**
     * @param array $interfaces
     */
    public function setInterfaces(array $interfaces): void
    {
        $this->interfaces = new Interfaces();
        foreach ($interfaces as $interface) {
            $fullClassName = $interface;
            $alias = $this->getImports()->add($interface);
            $this->interfaces[$fullClassName] = $alias;
        }
    }

    /**
     * @return Properties
     */
    public function getProperties(): Properties
    {
        return $this->properties;
    }

    /**
     * @param Properties $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = new Properties($properties, $this->getImports());

        /** @var Property $property */
        foreach ($this->properties as $property) {
            if (!empty($property->getType())) {
                $property->setType($this->getImports()->add($property->getType()));
            }
        }
    }

    /**
     * @return Methods
     */
    public function getMethods(): Methods
    {
        return $this->methods;
    }

    /**
     * @param Methods|array $methods
     */
    public function setMethods($methods): void
    {
        if (is_array($methods)) {
            $methods = new Methods($methods);
        }
        if (!$methods instanceof Methods) {
            throw new InvalidArgumentException('Expected an instance of ' . Methods::class);
        }
        $this->methods = $methods;

        // If we have an abstract method, we cannot be final and we have to be abstract
        /** @var Method $method */
        foreach ($this->methods as $method) {
            if ($method->isAbstract()) {
                $this->setIsAbstract(true);
                break;
            }
            if (!empty($method->getReturnType())) {
                $method->setReturnType($this->getImports()->add($method->getReturnType()));
            }
            /** @var Param $parameter */
            foreach ($method->getParameters() as $parameter) {
                if (!empty($parameter->getType())) {
                    $parameter->setType($this->getImports()->add($parameter->getType()));
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        if ($type != self::TYPE_INTERFACE && $type != self::TYPE_TRAIT) {
            $type = self::TYPE_CLASS;
        } elseif ($this->isAbstract()) {
            $this->setIsAbstract(false);
        }
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->isAbstract;
    }

    /**
     * @param bool $isAbstract
     */
    public function setIsAbstract(bool $isAbstract): void
    {
        if (!$isAbstract) {
            /** @var Method $method */
            foreach ($this->getMethods() as $method) {
                if ($method->isAbstract()) {
                    throw new InvalidArgumentException('Cannot make a class with abstract methods not abstract');
                    break;
                }
            }
        }
        if ($isAbstract) {
            $this->setIsFinal(false);
        }
        $this->isAbstract = $isAbstract;
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->isFinal;
    }

    /**
     * @param bool $isFinal
     */
    public function setIsFinal(bool $isFinal): void
    {
        if ($isFinal && $this->isAbstract()) {
            return;
        }
        $this->isFinal = $isFinal;
    }

    /**
     * @return Imports
     */
    public function getImports(): Imports
    {
        return $this->imports;
    }

    /**
     * @param Imports|array $imports
     */
    public function setImports($imports): void
    {
        foreach ($imports as $fullClassName => $alias) {
            if (is_numeric($fullClassName)) {
                $fullClassName = $alias;
                $alias = null;
            } elseif (strpos($alias, '\\') !== false && strpos($fullClassName, '\\') == false) {
                $tmp = $fullClassName;
                $fullClassName = $alias;
                $alias = $tmp;
            }
            $this->imports->offsetSet($fullClassName, $alias);
        }
    }

    /**
     * @return Features
     */
    public function getFeatures(): Features
    {
        return $this->features;
    }

    /**
     * @param array $features
     */
    public function setFeatures(array $features): void
    {
        $this->features = new Features($features);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // Namespace
        $return = 'namespace ' . $this->getNamespace() . ';' . PHP_EOL . PHP_EOL;

        if ($this->getImports()->count() > 0) {
            $return.= $this->getImports() . PHP_EOL . PHP_EOL;
        }

        // Class DocBlock
        $docBlock = new Docblock($this->getDescription());
        $return .= (string)$docBlock . PHP_EOL;

        // Class descriptor
        if ($this->isAbstract()) {
            $return .= 'abstract ';
        } elseif ($this->isFinal()) {
            $return .= 'final ';
        }
        $return.= $this->getType() . ' ' . $this->getName();

        $return.= strval($this->getParents());
        $return.= strval($this->getInterfaces());

        // Opening bracket
        $return.= PHP_EOL . '{' . PHP_EOL;
        // TODO: Traits
        // TODO: Constants
        if ($this->getProperties()->count() > 0) {
            $return .= Indenter::indent($this->getProperties(), 1) . PHP_EOL;
        }
        if ($this->getProperties()->count() > 0 && $this->getMethods()->count() > 0) {
            $return.= PHP_EOL;
        }
        if ($this->getMethods()->count() > 0) {
            $return .= Indenter::indent($this->getMethods(), 1) . PHP_EOL;
        }

        // Closing bracket
        $return.= '}' . PHP_EOL;

        // Return string
        return $return;
    }
}
