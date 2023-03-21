<?php

namespace Tochka\Hydrator\Tests\Units\Definitions;

use Tochka\Hydrator\Definitions\ClassDefinitionsRegistry;
use PHPUnit\Framework\TestCase;
use Tochka\Hydrator\Definitions\DTO\ClassDefinition;

class ClassDefinitionsRegistryTest extends TestCase
{
    public function testAdd(): void
    {
        $expectedFoo = new ClassDefinition('FooClass');
        $expectedBar = new ClassDefinition('BarClass');

        $registry = new ClassDefinitionsRegistry();
        $registry->add($expectedFoo);
        $registry->add($expectedBar);

        self::assertTrue($registry->has('FooClass'));
        self::assertTrue($registry->has('BarClass'));
        self::assertFalse($registry->has('NoClass'));

        self::assertEquals($expectedFoo, $registry->get('FooClass'));
        self::assertEquals($expectedBar, $registry->get('BarClass'));
        self::assertNull($registry->get('NoClass'));

        self::assertEquals(['FooClass' => $expectedFoo, 'BarClass' => $expectedBar], $registry->getAll());
    }
}
