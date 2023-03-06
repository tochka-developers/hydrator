<?php

namespace Tochka\Hydrator\Exceptions;

class InternalHydratorException extends BaseHydratorException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, HydratorExceptionInterface::CODE_INTERNAL, $previous);
    }
}
