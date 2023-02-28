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
class TimeZone
{
    public string $timezone;

    public function __construct(string $timezone)
    {
        $this->timezone = $timezone;
    }
}
