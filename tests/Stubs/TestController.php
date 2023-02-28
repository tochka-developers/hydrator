<?php

namespace Tochka\Hydrator\Tests\Stubs;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class TestController
{
    /**
     * @param string $param1 параметр 1
     * @param FooObject $param2 параметр 2
     * @return Collection<Carbon|FooObject> Результат
     */
    public function hello(string $param1, FooObject $param2, int $param3 = 0): Collection
    {
    }
}
