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
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ExtractByMethod
{
    public string $methodName;

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }
}
