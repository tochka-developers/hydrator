<?php

namespace Tochka\Hydrator\Annotations;

use Spiral\Attributes\NamedArgumentConstructor;

/**
 * @psalm-api
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER)]
class CastBy
{
    /** @var class-string */
    public string $casterClassName;

    /**
     * @param class-string $casterClassName
     */
    public function __construct(string $casterClassName)
    {
        $this->casterClassName = $casterClassName;
    }
}
