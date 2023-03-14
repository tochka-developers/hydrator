<?php

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

class MakeTargetError extends Error
{
    public const CODE = 'make_target';
    public const MESSAGE = 'Error while make target object [%s]';

    public function __construct(string $className, Context $context)
    {
        parent::__construct(
            self::CODE,
            sprintf(self::MESSAGE, $className),
            $context
        );
    }
}
