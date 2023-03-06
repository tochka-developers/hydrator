<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Definitions\DTO\Collection;

class ClassDefinition
{
    /** @var class-string */
    private string $className;
    /** @var array<PropertyDefinition> */
    private array $properties = [];
    private ?string $description = null;
    private Caster $caster;
    /** @var Collection<object> */
    private Collection $attributes;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->caster = new Caster();
        $this->attributes = new Collection();
    }

    /**
     * @psalm-mutation-free
     * @return class-string
     */
    #[Pure]
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @psalm-mutation-free
     * @return array<PropertyDefinition>
     */
    #[Pure]
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array<PropertyDefinition> $properties
     * @return void
     */
    public function setProperties(array $properties): void
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
     */
    #[Pure]
    public function getCaster(): Caster
    {
        return $this->caster;
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
}
