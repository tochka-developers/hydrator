<?php

namespace Tochka\Hydrator\DTO;

use JetBrains\PhpStorm\Pure;

interface NullableTypeInterface
{
    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function isNullable(): bool;

    public function setNullable(bool $nullable): void;
}
