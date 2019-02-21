<?php

namespace Amz\Code\Php;

use Amz\Core\Contracts\ArrayConstructable;
use Amz\Core\Contracts\CreatableFromArray;
use Amz\Core\Contracts\Extractable;

class Features implements ArrayConstructable, CreatableFromArray, Extractable
{
    /** @var bool */
    protected $hasParameterConstructor = false;

    /** @var bool */
    protected $hasArrayConstructor = false;

    /** @var bool */
    protected $hasNoGetter = false;
    /** @var bool */
    protected $hasPublicGetterScope = false;
    /** @var bool */
    protected $hasProtectedGetterScope = false;
    /** @var bool */
    protected $hasPrivateGetterScope = false;

    /** @var bool */
    protected $hasNoSetter = false;
    /** @var bool */
    protected $hasPublicSetterScope = false;
    /** @var bool */
    protected $hasProtectedSetterScope = false;
    /** @var bool */
    protected $hasPrivateSetterScope = false;

    public function __construct($payload)
    {
        if (boolval($payload['has_parameter_constructor'] ?? false)) {
            $this->setHasParameterConstructor(true);
        } elseif (boolval($payload['has_array_constructor'] ?? false)) {
            $this->setHasArrayConstructor(true);
        }

        // Getter
        if ($payload['has_no_getter'] ?? false) {
            $this->setHasNoGetter(true);
        } elseif ($payload['has_private_getter_scope'] ?? false) {
            $this->setHasPrivateGetterScope(true);
        } elseif ($payload['has_protected_getter_scope'] ?? false) {
            $this->setHasProtectedGetterScope(true);
        } else {
            $this->setHasPublicGetterScope(true);
        }

        // Setter
        if ($payload['has_no_setter'] ?? false) {
            $this->setHasNoSetter(true);
        } elseif ($payload['has_private_setter_scope'] ?? false) {
            $this->setHasPrivateSetterScope(true);
        } elseif ($payload['has_protected_setter_scope'] ?? false) {
            $this->setHasProtectedSetterScope(true);
        } else {
            $this->setHasPublicSetterScope(true);
        }
    }

    public static function fromArray(array $payload)
    {
        return new static($payload);
    }

    public function getArrayCopy(array $options = []): array
    {
        $return = [];

        // Constructor
        if ($this->hasParameterConstructor()) {
            $return['has_parameter_constructor'] = true;
        } elseif ($this->hasArrayConstructor()) {
            $return['has_array_constructor'] = true;
        }

        // Getter
        if ($this->hasNoGetter()) {
            $return['has_no_getter'] = true;
        } elseif ($this->hasPublicGetterScope()) {
            $return['has_public_getter_scope'] = true;
        } elseif ($this->hasProtectedGetterScope()) {
            $return['has_protected_getter_scope'] = true;
        } elseif ($this->hasPrivateGetterScope()) {
            $return['has_private_getter_scope'] = true;
        }

        // Setter
        if ($this->hasNoSetter()) {
            $return['has_no_setter'] = true;
        } elseif ($this->hasPublicSetterScope()) {
            $return['has_public_setter_scope'] = true;
        } elseif ($this->hasProtectedSetterScope()) {
            $return['has_protected_setter_scope'] = true;
        } elseif ($this->hasPrivateSetterScope()) {
            $return['has_private_setter_scope'] = true;
        }
        return $return;
    }

    /**
     * @return bool
     */
    public function hasParameterConstructor(): bool
    {
        return $this->hasParameterConstructor;
    }

    /**
     * @param bool $hasParameterConstructor
     */
    public function setHasParameterConstructor(bool $hasParameterConstructor): void
    {
        $this->hasParameterConstructor = $hasParameterConstructor;
        if ($hasParameterConstructor === true) {
            $this->hasArrayConstructor = false;
        }
    }

    /**
     * @return bool
     */
    public function hasArrayConstructor(): bool
    {
        return $this->hasArrayConstructor;
    }

    /**
     * @param bool $hasArrayConstructor
     */
    public function setHasArrayConstructor(bool $hasArrayConstructor): void
    {
        $this->hasArrayConstructor = $hasArrayConstructor;
        if ($hasArrayConstructor === true) {
            $this->hasParameterConstructor = false;
        }
    }

    /**
     * @return string|null
     */
    public function getGetterScope(): ?string
    {
        if ($this->hasNoGetter()) {
            return null;
        } elseif ($this->hasPrivateGetterScope()) {
            return Scope::PRIVATE;
        } elseif ($this->hasProtectedGetterScope()) {
            return Scope::PROTECTED;
        } elseif ($this->hasPublicGetterScope()) {
            return Scope::PUBLIC;
        }
    }

    /**
     * @return bool
     */
    public function hasNoGetter(): bool
    {
        return $this->hasNoGetter;
    }

    /**
     * @param bool $hasNoGetter
     */
    public function setHasNoGetter(bool $hasNoGetter): void
    {
        $this->hasNoGetter = $hasNoGetter;
        if ($hasNoGetter === true) {
            $this->hasPublicGetterScope = false;
            $this->hasPrivateGetterScope = false;
            $this->hasProtectedGetterScope = false;
        }
    }

    /**
     * @return bool
     */
    public function hasPublicGetterScope(): bool
    {
        return $this->hasPublicGetterScope;
    }

    /**
     * @param bool $hasPublicGetterScope
     */
    public function setHasPublicGetterScope(bool $hasPublicGetterScope): void
    {
        $this->hasPublicGetterScope = $hasPublicGetterScope;
        if ($hasPublicGetterScope === true) {
            $this->hasNoGetter = false;
            $this->hasPrivateGetterScope = false;
            $this->hasProtectedGetterScope = false;
        }
    }

    /**
     * @return bool
     */
    public function hasProtectedGetterScope(): bool
    {
        return $this->hasProtectedGetterScope;
    }

    /**
     * @param bool $hasProtectedGetterScope
     */
    public function setHasProtectedGetterScope(bool $hasProtectedGetterScope): void
    {
        $this->hasProtectedGetterScope = $hasProtectedGetterScope;
        if ($hasProtectedGetterScope === true) {
            $this->hasNoGetter = false;
            $this->hasPublicGetterScope = false;
            $this->hasPrivateGetterScope = false;
        }
    }

    /**
     * @return bool
     */
    public function hasPrivateGetterScope(): bool
    {
        return $this->hasPrivateGetterScope;
    }

    /**
     * @param bool $hasPrivateGetterScope
     */
    public function setHasPrivateGetterScope(bool $hasPrivateGetterScope): void
    {
        $this->hasPrivateGetterScope = $hasPrivateGetterScope;
        if ($hasPrivateGetterScope === true) {
            $this->hasNoGetter = false;
            $this->hasPublicGetterScope = false;
            $this->hasProtectedGetterScope = false;
        }
    }

    /**
     * @return string|null
     */
    public function getSetterScope(): ?string
    {
        if ($this->hasNoSetter()) {
            return null;
        } elseif ($this->hasPrivateSetterScope()) {
            return Scope::PRIVATE;
        } elseif ($this->hasProtectedSetterScope()) {
            return Scope::PROTECTED;
        } elseif ($this->hasPublicSetterScope()) {
            return Scope::PUBLIC;
        }
    }

    /**
     * @return bool
     */
    public function hasNoSetter(): bool
    {
        return $this->hasNoSetter;
    }

    /**
     * @param bool $hasNoSetter
     */
    public function setHasNoSetter(bool $hasNoSetter): void
    {
        $this->hasNoSetter = $hasNoSetter;
        if ($hasNoSetter === true) {
            $this->hasPublicSetterScope = false;
            $this->hasPrivateSetterScope = false;
            $this->hasProtectedSetterScope = false;
        }
    }

    /**
     * @return bool
     */
    public function hasPublicSetterScope(): bool
    {
        return $this->hasPublicSetterScope;
    }

    /**
     * @param bool $hasPublicSetterScope
     */
    public function setHasPublicSetterScope(bool $hasPublicSetterScope): void
    {
        $this->hasPublicSetterScope = $hasPublicSetterScope;
        if ($hasPublicSetterScope === true) {
            $this->hasNoSetter = false;
            $this->hasPrivateSetterScope = false;
            $this->hasProtectedSetterScope = false;
        }
    }

    /**
     * @return bool
     */
    public function hasProtectedSetterScope(): bool
    {
        return $this->hasProtectedSetterScope;
    }

    /**
     * @param bool $hasProtectedSetterScope
     */
    public function setHasProtectedSetterScope(bool $hasProtectedSetterScope): void
    {
        $this->hasProtectedSetterScope = $hasProtectedSetterScope;
        if ($hasProtectedSetterScope === true) {
            $this->hasNoSetter = false;
            $this->hasPublicSetterScope = false;
            $this->hasPrivateSetterScope = false;
        }
    }

    /**
     * @return bool
     */
    public function hasPrivateSetterScope(): bool
    {
        return $this->hasPrivateSetterScope;
    }

    /**
     * @param bool $hasPrivateSetterScope
     */
    public function setHasPrivateSetterScope(bool $hasPrivateSetterScope): void
    {
        $this->hasPrivateSetterScope = $hasPrivateSetterScope;
        if ($hasPrivateSetterScope === true) {
            $this->hasNoSetter = false;
            $this->hasPublicSetterScope = false;
            $this->hasProtectedSetterScope = false;
        }
    }

}
