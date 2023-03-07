<?php

namespace Tochka\Hydrator\Exceptions;

class MethodNotDefinedException extends BaseHydratorException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, ErrorCodesEnum::METHOD_NOT_DEFINED, $previous);
    }
}
