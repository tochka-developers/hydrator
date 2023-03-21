<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

/**
 * @psalm-api
 */
class ValueError extends Error
{
    public const CODE = 'unexpected_value';
    public const MESSAGE = 'Unexpected value. Actual: [%s], expected: [%s]';

    public readonly string|int $actual;
    public readonly string|int $expected;

    public function __construct(string|int $actual, string|int $expected, Context $context)
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
