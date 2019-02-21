<?php

namespace Amz\Code\Php;

use Amz\Core\Contracts\CreatableFromArray;
use Amz\Core\Contracts\Extractable;
use Amz\Core\Contracts\Named;
use Amz\Core\Support\Util\Arr;

class Param implements CreatableFromArray, Named, Extractable
{
    /**
     * The name of the parameter, used in the method descriptor (will be sanitized)
     *
     * @var string
     */
    protected $name;

    /**
     * The parameter type to be used, if not provided "mixed" will be assumed
     *
     * @var string
     */
    protected $type = 'mixed';

    /**
     * Is a null value allowed? Defaults to false
     *
     * @var bool|null
     */
    protected $canBeNull = false;

    /**
     * Default value, makes the parameter optional
     *
     * @var mixed|null
     */
    protected $defaultValue;

    /**
     * Description of the parameter
     *
     * @var string|null
     */
    protected $description;

    /**
     * Param constructor.
     * @param string $name
     * @param string $type
     * @param mixed|null $defaultValue
     * @param bool $canBeNull
     * @param string|null $description
     */
    public function __construct(
        string $name,
        string $type = 'mixed',
        $defaultValue = null,
        bool $canBeNull = false,
        ?string $description = null
    ) {
        if (!is_null($name)) {
            $this->setName(NamingStrategy::getParamName($name));
        }
        if (!is_null($type)) {
            $this->setType($type);
        }
        if (!is_null($canBeNull)) {
            $this->setCanBeNull($canBeNull);
        }
        if (!is_null($defaultValue)) {
            $this->setDefaultValue($defaultValue);
        }
        if (!is_null($description)) {
            $this->setDescription($description);
        }
    }

    /**
     * Constructor
     *
     * @param string $name
     * @param string $type
     * @param bool|null $canBeNull
     * @param mixed|null $defaultValue
     * @param string|null $description
     * @return void
     */
    protected function __constructor(string $name, string $type, ?bool $canBeNull = true, $defaultValue = null, ?string $description = null): void
    {
        if (!is_null($name)) {
            $this->setName($name);
        }
        if (!is_null($type)) {
            $this->setType($type);
        }
        if (!is_null($canBeNull)) {
            $this->setCanBeNull($canBeNull);
        }
        if (!is_null($defaultValue)) {
            $this->setDefaultValue($defaultValue);
        }
        if (!is_null($description)) {
            $this->setDescription($description);
        }
    }

    /**
     * @param array $payload
     * @return Property
     */
    public static function fromArray(array $payload): Param
    {
        return new static(
            $payload['name']          ?? null,
            $payload['type']          ?? 'mixed',
            $payload['default_value'] ?? null,
            $payload['can_be_null']   ?? false,
            $payload['description']   ?? null,
        );
    }

    /**
     * @param array $options
     * @return array
     */
    public function getArrayCopy(array $options = []): array
    {
        return [
            'name'          => $this->getName(),
            'type'          => $this->getType(),
            'default_value' => $this->getDefaultValue(),
            'can_be_null'   => $this->canBeNull(),
            'description'   => $this->getDescription(),
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
     * Getter for $name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setter for $name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Getter for $type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Setter for $type
     *
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Getter for $canBeNull
     *
     * @return bool|null
     */
    public function canBeNull(): ?bool
    {
        return $this->canBeNull;
    }

    /**
     * Setter for $canBeNull
     *
     * @param bool|null $canBeNull
     * @return void
     */
    public function setCanBeNull(?bool $canBeNull): void
    {
        $this->canBeNull = $canBeNull;
    }

    /**
     * Getter for $defaultValue
     *
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Setter for $defaultValue
     *
     * @param mixed|null $defaultValue
     * @return void
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * Getter for $description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for $description
     *
     * @param string|null $description
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // Process the type, optional if needed
        $type = $this->getType() ?? 'mixed';
        if ($type != 'mixed' && strpos($type, '|') === false) {
            if ($this->canBeNull()) {
                $type = '?' . $type;
            }
            $type = $type . ' ';
        } else {
            $type = '';
        }

        // Add the variable
        $name = $this->getName();

        // Add the defaultValue if needed
        $default = $this->getDefaultValue();
        if (!is_null($default)) {
            if ($default === ':null:') {
                $default = 'null';
            } elseif (is_array($default)) {
                $default = Arr::export($default, true);
            } else {
                $default = var_export($default, true);
            }
            $default = ' = ' . $default;
        } else {
            $default = '';
        }

        // return the string
        return <<<CODE
            {$type}\${$name}{$default}
            CODE;
    }
}
