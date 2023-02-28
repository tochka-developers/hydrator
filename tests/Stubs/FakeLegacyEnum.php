<?php

namespace Tochka\Hydrator\Tests\Stubs;

use Tochka\Hydrator\Support\LegacyEnum;

class FakeLegacyEnum extends LegacyEnum
{
    public const FOO = 'foo';
    public const BAR = 'bar';

    public static function FOO(): self
    {
        return new self(self::FOO);
    }

    public static function BAR(): self
    {
        return new self(self::BAR);
    }
}
