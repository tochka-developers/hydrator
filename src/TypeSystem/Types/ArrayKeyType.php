<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\TypeAliasInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @implements TypeAliasInterface<array-key>
 */
final class ArrayKeyType implements TypeAliasInterface
{
    public function type(): TypeInterface
    {
        return new UnionType(new IntType(), new StringType());
    }
}
