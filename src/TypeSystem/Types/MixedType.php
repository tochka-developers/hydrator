<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @implements TypeInterface<mixed>
 */
final class MixedType implements TypeInterface
{
    public function __toString(): string
    {
        return 'mixed';
    }
}
