<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @implements TypeInterface<int>
 */
final class IntType implements TypeInterface
{
    public readonly ?int $min;
    public readonly ?int $max;

    public function __construct(?int $min = null, ?int $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function __toString(): string
    {
        return 'int';
    }
}
