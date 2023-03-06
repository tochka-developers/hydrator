<?php

namespace Tochka\Hydrator\Exceptions;

abstract class BaseHydratorException extends \RuntimeException implements HydratorExceptionInterface
{
    public function __construct(string $message, ErrorCodesEnum $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code->value, $previous);
    }
}
