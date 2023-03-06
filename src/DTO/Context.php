<?php

namespace Tochka\Hydrator\DTO;

class Context
{
    private readonly string $name;
    private readonly ?Context $previous;

    public function __construct(string $name, ?Context $previous = null)
    {
        $this->name = $name;
        $this->previous = $previous;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrevious(): ?Context
    {
        return $this->previous;
    }

    public function toString(): string
    {
        return implode('.', array_filter([$this->getPrevious()?->toString(), $this->getName()]));
    }
}
