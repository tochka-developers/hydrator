<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @template-covariant TKey of array-key
 * @template-covariant TValue
 * @implements TypeInterface<array<TKey, TValue>>
 */
final class ArrayType implements TypeInterface
{
    public readonly TypeInterface $keyType;
    public readonly TypeInterface $valueType;

    /**
     * @param TypeInterface<TKey> $keyType
     * @param TypeInterface<TValue> $valueType
     */
    public function __construct(TypeInterface $keyType = new ArrayKeyType(), TypeInterface $valueType = new MixedType())
    {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
    }

    public function __toString(): string
    {
        return sprintf('array<%s,%s>', $this->keyType, $this->valueType);
    }
}
