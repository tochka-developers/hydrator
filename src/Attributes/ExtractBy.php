<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Attributes;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class and method for extracting value to this property/parameter.
 *
 * Default method name is `extract`.
 * If the class name is not specified, then the extract method from the current class will be used.
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
final class ExtractBy
{
    /**
     * @param class-string|null $className
     */
    public function __construct(
        public readonly ?string $className = null,
        public readonly string $methodName = 'extract'
    ) {
    }
}
