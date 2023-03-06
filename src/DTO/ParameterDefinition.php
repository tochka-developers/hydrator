<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;
use Tochka\Hydrator\Definitions\DTO\Collection;

/**
 * @template TDefaultValue
 */
class ParameterDefinition extends ValueDefinition
{
    private string $name;
    private bool $required = false;
    private mixed $defaultValue = null;
    private bool $hasDefaultValue = false;
    private Caster $caster;
    /** @var Collection<object> */
    private Collection $attributes;

    public function __construct(string $name, TypeDefinition|UnionTypeDefinition $type)
    {
        parent::__construct($type);
        $this->name = $name;
        $this->caster = new Caster();
        $this->attributes = new Collection();
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getName(): string
    {
        return $this->name;
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
     * @psalm-mutation-free
     */
    #[Pure]
    public function getCaster(): Caster
    {
        return $this->caster;
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
}
