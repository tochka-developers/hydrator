<?php

namespace Tochka\Hydrator\Exceptions;

class MakeCasterException extends BaseHydratorException
{
    public const CODE = 50101;
    public const MESSAGE = 'Error while make caster';

    public function __construct(?string $message = null, ?\Throwable $previous = null)
    {
        parent::__construct($message ?? self::MESSAGE, self::CODE, $previous);
    }
}
