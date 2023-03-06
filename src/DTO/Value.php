<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;

class Value
{
    private mixed $value;
    private TypeDefinition $type;

    public function __construct(mixed $value, TypeDefinition $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getType(): TypeDefinition
    {
        return $this->type;
    }
}
