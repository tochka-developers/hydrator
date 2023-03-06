<?php

namespace Tochka\Hydrator\ExtendedReflection;

use Tochka\Hydrator\TypeSystem\TypeInterface;

interface ExtendedReflectionWithTypeInterface extends ExtendedReflectionInterface
{
    public function getType(): TypeInterface;
}
