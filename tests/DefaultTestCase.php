<?php

namespace Tochka\Hydrator\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Orchestra\Testbench\TestCase;
use Tochka\Hydrator\CodeParserServiceProvider;

abstract class DefaultTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function getPackageProviders($app): array
    {
        return [CodeParserServiceProvider::class];
    }
}
