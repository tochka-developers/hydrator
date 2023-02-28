<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;

class CastInfo
{
    private ?TypeDefinition $typeDefinition;
    private ?ValueDefinition $valueDefinition;
    private ?ClassDefinition $classDefinition;
    /** @var Collection<object> */
    private Collection $attributes;

    public function __construct(
        ?TypeDefinition $typeDefinition = null,
        ?ValueDefinition $valueDefinition = null,
        ?ClassDefinition $classDefinition = null,
        ?Collection $attributes = null
    ) {
        $this->typeDefinition = $typeDefinition;
        $this->valueDefinition = $valueDefinition;
        $this->classDefinition = $classDefinition;
        $this->attributes = $attributes ?? new Collection();
    }

    #[Pure]
    public function getTypeDefinition(): ?TypeDefinition
    {
        return $this->typeDefinition;
    }

    #[Pure]
    public function getValueDefinition(): ?ValueDefinition
    {
        return $this->valueDefinition;
    }

    #[Pure]
    public function getClassDefinition(): ?ClassDefinition
    {
        return $this->classDefinition;
    }

    /**
     * @return Collection<object>
     */
    #[Pure]
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }
}
