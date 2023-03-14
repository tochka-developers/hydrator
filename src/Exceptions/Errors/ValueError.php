<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

class ValueError extends Error
{
    public const CODE = 'unexpected_value';
    public const MESSAGE = 'Unexpected value. Actual: [%s], expected: [%s]';

    public readonly string $actual;
    public readonly string $expected;

    public function __construct(string $actual, string $expected, Context $context)
    {
        $this->expected = $expected;
        $this->actual = $actual;

        parent::__construct(
            self::CODE,
            sprintf(self::MESSAGE, $actual, $expected),
            $context
        );
    }
}
