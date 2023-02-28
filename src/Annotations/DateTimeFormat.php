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
class DateTimeFormat
{
    public string $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }
}
