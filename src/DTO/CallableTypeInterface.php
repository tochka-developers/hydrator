<?php

namespace Tochka\Hydrator\DTO;

interface CallableTypeInterface
{
    /**
     * @param callable(TypeDefinition): void $callback
     */
    public function call(callable $callback): void;
}
