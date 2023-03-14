<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Attributes;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class and method for deciding which type from the Union type to extract or hydrate the value into
 *
 * Default method name is `resolve`.
 * If the class name is not specified, then the resolve method from the current class will be used.
 *
 * Method Type: `(mixed $value, TypeInterface $type): bool`
 *
 * @psalm-api
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER)]
#[NamedArgumentConstructor]
final class ResolveBy
{
    /**
     * @param class-string|null $className
     */
    public function __construct(
        public readonly ?string $className = null,
        public readonly string $methodName = 'resolve',
    ) {
    }
}
