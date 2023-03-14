<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use Tochka\Hydrator\Exceptions\Enums\ExceptionCodeEnum;

class ContainerException extends BaseHydrateException
{
    public function __construct(string $message, ?ContainerExceptionInterface $previous = null)
    {
        parent::__construct($message, ExceptionCodeEnum::EXTENDED_TYPE_FACTORY, $previous);
    }
}
