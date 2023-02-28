<?php

namespace Tochka\Hydrator\Exceptions;

class CasterImplementsException extends BaseHydratorException
{
    public const CODE = 50102;
    public const MESSAGE = 'Caster [%s] must implement [%s]';

    public function __construct(string $className, string $shouldImplements)
    {
        parent::__construct(sprintf(self::MESSAGE, $className, $shouldImplements), self::CODE);
    }
}
