<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @template-covariant TObject of object
 * @implements TypeInterface<TObject>
 */
final class NamedObjectType implements TypeInterface
{
    /** @var class-string<TObject> */
    public readonly string $className;

    /**
     * @param class-string<TObject> $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }
}
