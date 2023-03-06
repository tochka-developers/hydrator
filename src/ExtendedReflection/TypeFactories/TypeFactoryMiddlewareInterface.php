<?php

namespace Tochka\Hydrator\ExtendedReflection\TypeFactories;

use Tochka\Hydrator\ExtendedReflection\ExtendedReflectionWithTypeInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;

interface TypeFactoryMiddlewareInterface
{
    /**
     * @param callable(TypeInterface, ExtendedReflectionWithTypeInterface): TypeInterface $next
     */
    public function handle(
        TypeInterface $defaultType,
        ExtendedReflectionWithTypeInterface $reflector,
        callable $next
    ): TypeInterface;
}
