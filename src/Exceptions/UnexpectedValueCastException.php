<?php

namespace Tochka\Hydrator\Exceptions;

class UnexpectedValueCastException extends CastException
{
    public const MESSAGE = 'Unexpected value while cast. Actual: [%s], expected: [%s]';

    public function __construct(mixed $actualValue, array $expectedValues)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $actualValue, implode(',', $expectedValues)),
            [
                'actual_value' => $actualValue,
                'expected_values' => $expectedValues
            ]
        );
    }
}
