<?php

namespace Tochka\Hydrator\ExtendedReflection;

use Tochka\Hydrator\TypeSystem\TypeInterface;

interface ExtendedValueReflectionInterface extends ExtendedReflectionInterface
{
    public function getType(): TypeInterface;

    public function hasDefaultValue(): bool;

    public function isRequired(): bool;

    public function getDefaultValue(): mixed;
}
