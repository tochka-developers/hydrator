<?php

namespace Tochka\Hydrator\Tests\Stubs;

use Carbon\Carbon;

class BarObject
{
    /** @var array<array<FooObject>> */
    public array $barTest1;
    public bool $barTest2;
    public Carbon $carbon;
}
