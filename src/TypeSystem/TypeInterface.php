<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem;

/**
 * @psalm-api
 * @psalm-immutable
 * @template-covariant TType
 */
interface TypeInterface
{
    public function __toString(): string;
}
