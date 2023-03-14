<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\Exceptions\Enums\ExceptionCodeEnum;

class MethodNotDefinedExceptionBase extends BaseHydrateException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, ExceptionCodeEnum::METHOD_NOT_DEFINED, $previous);
    }
}
