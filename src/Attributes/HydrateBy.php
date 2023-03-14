<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Attributes;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class and method for hydrate value from this property/parameter
 *
 * Default method name is `hydrate`.
 * If the class name is not specified, then the hydrate method from the current class will be used.
 *
 * Method Type: `(FromContainer $from, ToContainer $to, Context $context): mixed`
 *
 * @psalm-api
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER)]
#[NamedArgumentConstructor]
final class HydrateBy
{
    /**
     * @param class-string|null $className
     */
    public function __construct(
        public readonly ?string $className = null,
        public readonly string $methodName = 'hydrate'
    ) {
    }
}
