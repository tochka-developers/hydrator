<?php

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

class ContainerError extends Error
{
    public const CODE = 'container_error';

    public function __construct(string $message, Context $context)
    {
        parent::__construct(self::CODE, $message, $context);
    }
}
