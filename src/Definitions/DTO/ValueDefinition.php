<?php

namespace Tochka\Hydrator\Definitions\DTO;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\TypeSystem\TypeInterface;

class ValueDefinition
{
    private string $name;
    private TypeInterface $type;
    private bool $required = false;
    private mixed $defaultValue = null;
    private bool $hasDefaultValue = false;
    /** @var Collection<object> */
    private Collection $attributes;
    private ?string $description = null;

    public function __construct(string $name, TypeInterface $type)
    {
        $this->name = $name;
        $this->type = $type;
        $this->attributes = new Collection([]);
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(mixed $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
        $this->hasDefaultValue = true;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
