<?php

namespace Tochka\Hydrator\Definitions\DTO;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\TypeSystem\Types\MixedType;

class MethodDefinition implements DefinitionInterface
{
    /** @var class-string */
    private string $className;
    private string $methodName;
    /** @var Collection<ValueDefinition> */
    private Collection $parameters;
    private ReturnDefinition $returnDefinition;
    /** @var Collection<object> */
    private Collection $attributes;
    private ?string $description = null;

    /**
     * @param class-string $className
     */
    public function __construct(string $className, string $methodName)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        /** @var array<ValueDefinition> $parameters */
        $parameters = [];
        $this->parameters = new Collection($parameters);
        $this->attributes = new Collection([]);
        $this->returnDefinition = new ReturnDefinition(new MixedType());
    }

    /**
     * @psalm-mutation-free
     * @psalm-pure
     * @return class-string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @psalm-mutation-free
     * @return Collection<ValueDefinition>
     */
    #[Pure]
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    /**
     * @param Collection<ValueDefinition> $parameters
     */
    public function setParameters(Collection $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @psalm-mutation-free
     * @return Collection<object>
     */
    #[Pure]
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @param Collection<object> $attributes
     * @return void
     */
    public function setAttributes(Collection $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getReturnDefinition(): ReturnDefinition
    {
        return $this->returnDefinition;
    }

    public function setReturnDefinition(ReturnDefinition $returnDefinition): void
    {
        $this->returnDefinition = $returnDefinition;
    }
}
