<?php

namespace Tochka\Hydrator\Definitions\DTO;

use JetBrains\PhpStorm\Pure;

class ClassDefinition implements DefinitionInterface
{
    /** @var class-string */
    private string $className;
    /** @var Collection<ValueDefinition> */
    private Collection $properties;
    /** @var Collection<object> */
    private Collection $attributes;
    private ?string $description = null;
    private bool $virtual = false;
    private bool $isInterface = false;
    private bool $isEnum = false;
    private bool $isTrait = false;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        /** @var array<ValueDefinition> $properties */
        $properties = [];
        $this->properties = new Collection($properties);
        $this->attributes = new Collection([]);
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

    /**
     * @psalm-mutation-free
     * @return Collection<ValueDefinition>
     */
    #[Pure]
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    /**
     * @param Collection<ValueDefinition> $properties
     */
    public function setProperties(Collection $properties): void
    {
        $this->properties = $properties;
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

    public function isVirtual(): bool
    {
        return $this->virtual;
    }

    public function setVirtual(bool $virtual): void
    {
        $this->virtual = $virtual;
    }

    public function isInterface(): bool
    {
        return $this->isInterface;
    }

    public function setIsInterface(bool $isInterface): void
    {
        $this->isInterface = $isInterface;
    }

    public function isEnum(): bool
    {
        return $this->isEnum;
    }

    public function setIsEnum(bool $isEnum): void
    {
        $this->isEnum = $isEnum;
    }

    public function isTrait(): bool
    {
        return $this->isTrait;
    }

    public function setIsTrait(bool $isTrait): void
    {
        $this->isTrait = $isTrait;
    }
}
