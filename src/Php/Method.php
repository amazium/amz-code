<?php

namespace Amz\Code\Php;

use Amz\Code\Format\Indenter;
use Amz\Core\Contracts\CreatableFromArray;
use Amz\Core\Contracts\Extractable;
use Amz\Core\Contracts\Named;
use Amz\Core\Exception\InvalidArgumentException;
use Amz\Core\Support\Util\Str;
use PHPUnit\Framework\MockObject\Matcher\Parameters;

class Method implements CreatableFromArray, Named, Extractable
{
    /**
     * The method name
     *
     * @var string
     */
    protected $name;

    /**
     * The method return type, default mixed
     *
     * @var string
     */
    protected $returnType;

    /**
     * Can the method return a null value
     *
     * @var bool
     */
    protected $canReturnNull = false;

    /**
     * A description of the method
     *
     * @var string
     */
    protected $description;

    /**
     * The body of the method
     *
     * @var string
     */
    protected $body;

    /**
     * Method parameters
     *
     * @var Parameters
     */
    protected $parameters;

    /**
     * Exceptions that can be thrown by the method
     *
     * @var array
     */
    protected $exceptions = [];

    /**
     * The scope of the method
     *
     * @var string
     */
    protected $scope = Scope::PROTECTED;

    /**
     * Is this a static method?
     *
     * @var bool
     */
    protected $isStatic = false;

    /**
     * Is this an abstract method?
     *
     * @var bool
     */
    protected $isAbstract = false;

    /**
     * Is this a final method?
     *
     * @var bool
     */
    protected $isFinal = false;

    /**
     * Method constructor.
     * @param string $name
     * @param string $description
     * @param string|null $body
     * @param array $parameters
     * @param string|null $returnType
     * @param bool $canReturnNull
     * @param string $scope
     * @param bool $isAbstract
     * @param bool $isStatic
     * @param bool $isFinal
     * @param array $exceptions
     */
    public function __construct(
        string $name,
        string $description,
        ?string $body,
        $parameters = [],
        ?string $returnType = null,
        bool $canReturnNull = false,
        string $scope = Scope::PROTECTED,
        bool $isAbstract = false,
        bool $isStatic = false,
        bool $isFinal = false,
        array $exceptions = []
    ) {
        if (!is_null($body) && strlen($body) > 0) {
            $isAbstract = false;
        } elseif ($isAbstract && $isFinal) {
            $isFinal = false;
        }
        $this->setName(NamingStrategy::getMethodName($name));
        $this->setDescription($description);
        $this->setBody($body);
        $this->setParameters($parameters);
        if (is_null($returnType)) {
            $returnType = 'void';
            $canReturnNull = false;
        }
        $this->setReturnType($returnType);
        $this->setCanReturnNull($canReturnNull);
        $this->setScope($scope);
        $this->setIsAbstract($isAbstract);
        $this->setIsStatic($isStatic);
        $this->setIsFinal($isFinal);
        $this->setExceptions($exceptions);
    }

    /**
     * @param array $payload
     * @return method
     */
    public static function fromArray(array $payload): method
    {
        return new static(
            $payload['name'] ?? null,
            $payload['description'] ?? null,
            $payload['body'] ?? null,
            $payload['parameters'] ?? [],
            $payload['return_type'] ?? null,
            $payload['can_return_null'] ?? false,
            $payload['scope'] ?? Scope::PROTECTED,
            $payload['is_abstract'] ?? false,
            $payload['is_static'] ?? false,
            $payload['is_final'] ?? false,
            $payload['exceptions'] ?? []
        );
    }

    /**
     * @param array $options
     * @return array
     */
    public function getArrayCopy(array $options = []): array
    {
        $return = [
            'name'            => $this->getName(),
            'description'     => $this->getDescription(),
            'body'            => $this->getBody(),
            'parameters'      => $this->getParameters()->getArrayCopy($options),
            'return_type'     => $this->getReturnType(),
            'can_return_null' => $this->canReturnNull(),
            'scope'           => $this->getScope(),
            'is_abstract'     => $this->isAbstract(),
            'is_static'       => $this->isStatic(),
            'is_final'        => $this->isFinal(),
            'exceptions'      => $this->getExceptions(),
        ];
        if (!boolval($options[ Extractable::EXTOPT_INCLUDE_NULL_VALUES ] ?? false)) {
            $return = array_filter(
                $return,
                function ($value, $key) {
                    return !is_null($value);
                },
                ARRAY_FILTER_USE_BOTH
            );
        }
        return $return;
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
    public function getReturnType(): ?string
    {
        return $this->returnType;
    }

    /**
     * @param string $returnType
     */
    public function setReturnType(?string $returnType): void
    {
        $this->returnType = $returnType;
    }

    /**
     * @return bool
     */
    public function canReturnNull(): bool
    {
        return $this->canReturnNull;
    }

    /**
     * @param bool $canReturnNull
     */
    public function setCanReturnNull(bool $canReturnNull): void
    {
        $this->canReturnNull = $canReturnNull;
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
     * @return string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return bool
     */
    public function getParameters(): Params
    {
        return $this->parameters;
    }

    /**
     * @param Params|array $parameters
     */
    public function setParameters($parameters): void
    {
        if (is_array($parameters)) {
            $parameters = new Params($parameters);
        }
        if (!$parameters instanceof Params) {
            throw new InvalidArgumentException(sprintf(
                'Excepted arguments to be of type array or %s',
                Params::class
            ));
        }
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * @param array $exceptions
     */
    public function setExceptions(array $exceptions): void
    {
        $this->exceptions = $exceptions;
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
     */
    public function setScope(string $scope): void
    {
        $this->scope = $scope;
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
     */
    public function setIsStatic(bool $isStatic): void
    {
        $this->isStatic = $isStatic;
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
        $this->isFinal = $isFinal;
    }

    /**
     * @return Docblock
     */
    protected function createDocblock(): Docblock
    {
        $docBlock = new Docblock($this->getDescription());

        /** @var Param $param */
        foreach ($this->getParameters() as $param) {
            $docBlock->addMethodParam(
                $param->getName(),
                $param->getType(),
                $param->canBeNull(),
                $param->getDescription() ?? null
            );
        }
        if ($this->getReturnType()) {
            $docBlock->addMethodReturnTag($this->getReturnType(), $this->canReturnNull());
        }
        foreach ($this->getExceptions() as $exception) {
            $docBlock->addMethodException($exception);
        }

        return $docBlock;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        // Docblock
        $docBlock = $this->createDocblock();

        // Function descriptor
        $prefix = $this->isAbstract() ? 'abstract ' : ($this->isFinal() ? 'final ' : '');
        $scope  = $this->getScope();
        $static = $this->isStatic() ? 'static ' : '';
        $name   = NamingStrategy::getMethodName($this->getName());
        $params = $this->getParameters();
        $return = '';
        if (boolval($this->getReturnType()) && $this->getReturnType() !== 'mixed') {
            $return = ': ';
            if ($this->canReturnNull()) {
                $return .= '?';
            }
            $return .= $this->getReturnType();
        }
        if ($this->isAbstract()) {
            $return .= ';';
        }

        // Add the body
        $body = '';
        if (!$this->isAbstract() && !is_null($this->getBody())) {
            $body  = '{' . PHP_EOL;
            $body .= Indenter::indent(trim($this->getBody()), 1) . PHP_EOL;
            $body .= '}';
        }

        // Return the string
        $code = <<<CODE
            {$docBlock}
            {$prefix}{$scope}{$static} function {$name}({$params}){$return}
            {$body}
            CODE;
        return $code;
    }

}
