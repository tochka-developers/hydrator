<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @template-covariant TType
 * @implements TypeInterface<TType>
 */
final class UnionType implements TypeInterface
{
    /** @var Collection<TypeInterface> */
    public readonly Collection $types;

    /**
     * @no-named-arguments
     * @param TypeInterface<TType> $type1
     * @param TypeInterface<TType> $type2
     * @param TypeInterface<TType> ...$types
     */
    public function __construct(TypeInterface $type1, TypeInterface $type2, TypeInterface ...$types)
    {
        $this->types = new Collection([$type1, $type2, ...$types]);
    }

    public function __toString(): string
    {
        return implode('|', $this->types->all());
    }
}
