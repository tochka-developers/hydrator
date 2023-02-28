<?php

namespace Tochka\Hydrator\Exceptions;

class WrongValueTypeCastException extends CastException
{
    public const MESSAGE = 'Wrong type while cast. Actual: [%s], expected: [%s]';

    public function __construct(mixed $actualType, string $expectedType)
    {
        parent::__construct(
            sprintf(self::MESSAGE, $actualType, $expectedType),
            [
                'actual_type' => $actualType,
                'expected_type' => $expectedType
            ]
        );
    }
}
