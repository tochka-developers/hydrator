<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @template-covariant TKey
 * @template-covariant TValue
 * @implements TypeInterface<iterable<TKey, TValue>>
 */
final class IterableType implements TypeInterface
{
    public readonly TypeInterface $keyType;
    public readonly TypeInterface $valueType;

    /**
     * @param TypeInterface<TKey> $keyType
     * @param TypeInterface<TValue> $valueType
     */
    public function __construct(TypeInterface $keyType = new MixedType(), TypeInterface $valueType = new MixedType())
    {
        $this->valueType = $valueType;
        $this->keyType = $keyType;
    }
}
