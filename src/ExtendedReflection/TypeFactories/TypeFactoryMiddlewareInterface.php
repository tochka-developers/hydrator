<?php

namespace Tochka\Hydrator\ExtendedReflection\TypeFactories;

use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;

interface TypeFactoryMiddlewareInterface
{
    /**
     * @param callable(TypeInterface, ExtendedReflectionInterface): TypeInterface $next
     */
    public function handle(
        TypeInterface $defaultType,
        ExtendedReflectionInterface $reflector,
        callable $next
    ): TypeInterface;
}
