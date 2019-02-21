<?php

namespace Amz\Code\Php;

class Docblock
{
    /** @var string */
    protected $description;

    /** @var array */
    protected $tags = [];

    /**
     * Docblock constructor.
     * @param string $description
     * @param array $tags
     */
    public function __construct(string $description, array $tags = [])
    {
        $this->description = $description;
        $this->tags = $tags;
    }

    /**
     * @param string $tag
     * @param mixed ...$extras
     */
    public function addTag(string $tag, ...$extras)
    {
        array_unshift($extras, $tag);
        $this->tags[] = $extras;
    }

    /**
     * @param string $returnType
     * @param bool $isOptional
     */
    public function addMethodReturnTag(string $returnType, bool $isOptional = false): void
    {
        $this->addTag(
            'return',
            $returnType . ($isOptional ? '|null' : '')
        );
    }

    /**
     * @param string $name
     * @param string $type
     * @param bool $isOptional
     * @param string $description
     */
    public function addMethodParam(
        string $name,
        string $type,
        bool $isOptional = false,
        ?string $description = ''
    ): void {
        $this->addTag(
            'param',
            $type . ($isOptional ? '|null' : ''),
            '$' . $name,
            $description
        );
    }

    /**
     * @param string $exception
     */
    public function addMethodException(string $exception): void
    {
        $this->addTag('throws', $exception);
    }

    /**
     * @param string $type
     * @param string $description
     */
    public function addVariable(string $type, bool $canBeNull): void
    {
        $this->addTag(
            'var',
            $type . ($canBeNull ? '|null' : '')
        );
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
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $description = str_replace(PHP_EOL, PHP_EOL . ' * ', PHP_EOL . $this->getDescription());
        $description = str_replace('* ' . PHP_EOL, '*' . PHP_EOL, $description);
        $return = '/**' . $description . PHP_EOL;
        if ($this->getTags()) {
            $return .= ' *' . PHP_EOL;
            foreach ($this->getTags() as $tag) {
                $return .= ' * @' . implode(' ', $tag) . PHP_EOL;
            }
        }
        $return.= ' */';
        return $return;
    }

}
