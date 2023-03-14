<?php

declare(strict_types=1);

namespace Tochka\Hydrator\DTO;

class ArrayContext extends Context
{
    public function __construct(
        public readonly string|int $arrayKey,
        ?string $className = null,
        ?Context $previous = null
    ) {
        parent::__construct((string)$arrayKey, $className, $previous);
    }

    public function getArrayKey(): string
    {
        return '[' . $this->name . ']';
    }

    public function __toString(): string
    {
        return ($this->previous ?? '') . $this->getArrayKey();
    }
}
