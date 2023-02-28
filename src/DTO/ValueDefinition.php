<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;

class ValueDefinition
{
    private TypeDefinition|UnionTypeDefinition $type;
    private ?string $description = null;

    public function __construct(TypeDefinition|UnionTypeDefinition $type)
    {
        $this->type = $type;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getType(): TypeDefinition|UnionTypeDefinition
    {
        return $this->type;
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
}
