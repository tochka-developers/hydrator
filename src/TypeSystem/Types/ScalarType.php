<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\TypeAliasInterface;
use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @extends TypeAliasInterface<scalar>
 */
final class ScalarType implements TypeAliasInterface
{
    public function type(): TypeInterface
    {
        return new UnionType(new BoolType(), new IntType(), new FloatType(), new StringType());
    }
}
