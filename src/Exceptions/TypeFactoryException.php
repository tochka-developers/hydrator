<?php

namespace Tochka\Hydrator\Exceptions;

class TypeFactoryException extends BaseHydratorException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, ErrorCodesEnum::EXTENDED_TYPE_FACTORY, $previous);
    }
}
