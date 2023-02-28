<?php

namespace Tochka\Hydrator\Tests\Units\Support;

use Tochka\Hydrator\Tests\DefaultTestCase;
use Tochka\Hydrator\Tests\Stubs\FakeLegacyEnum;

/**
 * @covers \Tochka\Hydrator\Support\LegacyEnum
 */
class LegacyEnumTest extends DefaultTestCase
{
    public function test__set_state(): void
    {
        $enum = FakeLegacyEnum::__set_state(['value' => 'foo']);

        self::assertEquals(FakeLegacyEnum::FOO(), $enum);
    }

    public function testGetValue(): void
    {
        $enum = FakeLegacyEnum::FOO();

        self::assertEquals(FakeLegacyEnum::FOO, $enum->getValue());
    }

    public function testIsNot(): void
    {
        $enum = FakeLegacyEnum::FOO();

        self::assertTrue($enum->isNot(FakeLegacyEnum::BAR()));
        self::assertFalse($enum->isNot(FakeLegacyEnum::FOO()));
    }

    public function testIs(): void
    {
        $enum = FakeLegacyEnum::FOO();

        self::assertFalse($enum->is(FakeLegacyEnum::BAR()));
        self::assertTrue($enum->is(FakeLegacyEnum::FOO()));
    }
}
