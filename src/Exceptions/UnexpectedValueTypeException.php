<?php

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;

class UnexpectedValueTypeException extends TransformingException
{
    public function __construct(string $expectedType, string $actualType, Context $context, ?\Throwable $previous)
    {
        parent::__construct($message, HydratorExceptionInterface::CODE_CONTEXT_ERROR, $context, $previous);
    }
}
