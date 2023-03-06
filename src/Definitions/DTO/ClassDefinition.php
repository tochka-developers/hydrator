<?php

namespace Tochka\Hydrator\Definitions\DTO;

use JetBrains\PhpStorm\Pure;

class ClassDefinition implements DefinitionInterface
{
    /** @var class-string */
    private string $className;
    /** @var Collection<PropertyDefinition> */
    private Collection $properties;
    /** @var Collection<object> */
    private Collection $attributes;
    private ?string $description = null;
    private bool $virtual = false;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        /** @var array<PropertyDefinition> $properties */
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
     * @return Collection<PropertyDefinition>
     */
    #[Pure]
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    /**
     * @param Collection<PropertyDefinition> $properties
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
}
