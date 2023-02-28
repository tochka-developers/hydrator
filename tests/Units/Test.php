<?php

namespace Tochka\Hydrator\Tests\Units;

use Illuminate\Container\Container;
use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Tests\DefaultTestCase;
use Tochka\Hydrator\Tests\Stubs\TestController;

class Test extends DefaultTestCase
{
    public function test()
    {
        $extractor = Container::getInstance()->make(ExtractorInterface::class);

        $data = (object)[

        ];
        $result = $extractor->extractMethodParameters($data, TestController::class, 'hello');
        dump($result);
    }
}
