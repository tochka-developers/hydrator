<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Attributes;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Alias for field/parameter extraction/hydration. This alias will be used instead of the field/parameter name
 *
 * @psalm-api
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER)]
#[NamedArgumentConstructor]
final class Alias
{
    public function __construct(
        public readonly string $alias
    ) {
    }
}
