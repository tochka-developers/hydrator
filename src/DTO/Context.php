<?php

declare(strict_types=1);

namespace Tochka\Hydrator\DTO;

class Context
{
    /**
     * @param class-string|null $className
     */
    public function __construct(
        public readonly string $name,
        private readonly ?string $className = null,
        public readonly ?Context $previous = null
    ) {
    }

    /**
     * @return class-string|null
     */
    public function getClassName(): ?string
    {
        return $this->className ?? $this->previous?->getClassName();
    }

    public function __toString(): string
    {
        return implode('.', array_filter([(string)$this->previous, $this->name]));
    }
}
