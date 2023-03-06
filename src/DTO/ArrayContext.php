<?php

namespace Tochka\Hydrator\DTO;

class ArrayContext extends Context
{
    public function __construct(string $arrayKey, ?Context $previous = null)
    {
        parent::__construct($arrayKey, $previous);
    }

    public function getArrayKey(): string
    {
        return '[' . $this->getName() . ']';
    }

    public function toString(): string
    {
        return ($this->getPrevious()?->toString() ?? '') . $this->getArrayKey();
    }
}
