<?php

namespace Tochka\Hydrator\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

abstract class DefaultTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;
}
