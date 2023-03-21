<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

/**
 * @psalm-api
 */
abstract class Error
{
    public readonly string $code;
    public readonly string $message;
    public readonly Context $context;

    public function __construct(string $code, string $message, Context $context)
    {
        $this->code = $code;
        $this->message = $message;
        $this->context = $context;
    }
}
