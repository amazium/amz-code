<?php

namespace Amz\Code\Php;

use Amz\Core\Contracts\CreatableFromArray;
use Amz\Core\Contracts\Extractable;
use Amz\Core\Contracts\Named;
use Amz\Core\Support\Util\Arr;
use Amz\Core\Support\Util\Str;

class Property implements CreatableFromArray, Named, Extractable
{
    /**
     * The property name
     *
     * @var string
     */
    protected $name;

    /**
     * The property type, default mixed
     *
     * @var string
     */
    protected $type = 'mixed';

    /**
     * Can the property be assigned a null value?
     *
     * @var bool
     */
    protected $isOptional = true;

    /**
     * Does the property have a default value
     *
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Default value during constructing the object
     *
     * @var mixed
     */
    protected $constructorDefaultValue = ':null:';

    /**
     * A description of the property
     *
     * @var string
     */
    protected $description;

    /**
     * The scope of the property
     *
     * @var string
     */
    protected $scope = Scope::PROTECTED;

    /**
     * Is this a static property?
     *
     * @var bool
     */
    protected $isStatic = false;

    /**
     * @var bool
     */
    protected $isCollection = false;

    /**
     * Property constructor.
     * @param string $name
     * @param string $description
     * @param string $type
     * @param null $defaultValue
     * @param bool $isOptional
     * @param string $scope
     * @param mixed $constructorDefaultValue
     * @param bool $isCollection
     */
    public function __construct(
        string $name,
        string $description,
        string $type = 'mixed',
        $defaultValue = null,
        bool $isOptional = true,
        string $scope = Scope::PROTECTED,
        $constructorDefaultValue = ':null:',
        bool $isCollection = false
    ) {
        $this->setName(NamingStrategy::getPropertyName($name));
        $this->setDescription($description);
        $this->setType($type);
        $this->setDefaultValue($defaultValue);
        $this->setIsOptional($isOptional);
        $this->setScope($scope);
        $this->setConstructorDefaultValue($constructorDefaultValue);
        $this->setIsCollection($isCollection);
    }

    /**
     * @param array $payload
     * @return Property
     */
    public static function fromArray(array $payload): Property
    {
        return new static(
            $payload['name'] ?? null,
            $payload['description'] ?? null,
            $payload['type'] ?? 'mixed',
            $payload['default_value'] ?? null,
            $payload['is_optional'] ?? true,
            $payload['scope'] ?? Scope::PROTECTED,
            $payload['constructor_default_value'] ?? ':null:',
            $payload['is_collection'] ?? false
        );
    }

    /**
     * @param array $options
     * @return array
     */
    public function getArrayCopy(array $options = []): array
    {
        return [
            'name'                      => $this->getName(),
            'description'               => $this->getDescription(),
            'type'                      => $this->getType(),
            'default_value'             => $this->getDefaultValue(),
            'constructor_default_value' => $this->getConstructorDefaultValue(),
            'is_optional'               => $this->isOptional(),
            'is_collection'             => $this->isCollection(),
            'scope'                     => $this->getScope(),
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * @param bool $isOptional
     */
    public function setIsOptional(bool $isOptional): void
    {
        $this->isOptional = $isOptional;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return mixed
     */
    public function getConstructorDefaultValue()
    {
        return $this->constructorDefaultValue;
    }

    /**
     * @param mixed $constructorDefaultValue
     * @return Property
     */
    public function setConstructorDefaultValue($constructorDefaultValue)
    {
        $this->constructorDefaultValue = $constructorDefaultValue;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    /**
     * @param bool $isStatic
     * @return Property
     */
    public function setIsStatic(bool $isStatic): Property
    {
        $this->isStatic = $isStatic;
        return $this;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return Property
     */
    public function setScope(string $scope): Property
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return $this->isCollection;
    }

    /**
     * @param bool $isCollection
     * @return Property
     */
    public function setIsCollection(bool $isCollection): Property
    {
        $this->isCollection = $isCollection;
        return $this;
    }

    /**
     * @return Docblock
     */
    protected function createDocblock(): Docblock
    {
        $docBlock = new Docblock($this->getDescription());
        $docBlock->addVariable($this->getType(), $this->isOptional());
        return $docBlock;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // Docblock
        $docBlock = strval($this->createDocblock());
        $scope    = $this->getScope();
        $static   = $this->isStatic() ? ' static' : '';
        $name     = $this->getName();

        // Default value
        $default = $this->getDefaultValue();
        if (!is_null($default)) {
            $default = is_array($default) ? Arr::export($default, false, 0) : var_export($default, true);
            $default = ' = ' . $default;
        } else {
            $default = '';
        }

        // Return the string
        return <<<CODE
            {$docBlock}
            {$scope}{$static} \${$name}{$default};
            CODE;
    }
}
