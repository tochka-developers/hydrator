<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\Exceptions\Enums\ExceptionCodeEnum;

abstract class BaseHydrateException extends \RuntimeException implements HydrateExceptionInterface
{
    public function __construct(string $message, ExceptionCodeEnum $code, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code->value, $previous);
    }
}
