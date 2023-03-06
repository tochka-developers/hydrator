<?php

namespace Tochka\Hydrator\Support;

use JetBrains\PhpStorm\Pure;

trait OneOfEnum
{
    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function oneOf(self ...$enums): bool
    {
        return in_array($this, $enums, true);
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function notOneOf(self ...$enums): bool
    {
        return !$this->oneOf(...$enums);
    }
}
