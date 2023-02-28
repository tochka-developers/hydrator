<?php

namespace Tochka\Hydrator\Support;

use JetBrains\PhpStorm\Pure;

abstract class LegacyEnum
{
    private string $value;

    protected function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function is(self $enum): bool
    {
        return $this->value === $enum->value;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function isNot(self $enum): bool
    {
        return !$this->is($enum);
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function in(self ...$enums): bool
    {
        foreach ($enums as $enum) {
            if ($this->is($enum)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @psalm-mutation-free
     */
    #[Pure]
    public function notIn(self ...$enums): bool
    {
        foreach ($enums as $enum) {
            if ($this->is($enum)) {
                return false;
            }
        }

        return true;
    }
}
