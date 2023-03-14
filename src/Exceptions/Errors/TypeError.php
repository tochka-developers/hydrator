<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions\Errors;

use Tochka\Hydrator\DTO\Context;

class TypeError extends Error
{
    public const CODE = 'unexpected_type';
    public const MESSAGE = 'Unexpected value type. Actual: [%s], expected: [%s]';

    public string $expectedTypes;
    public string $actualType;

    public function __construct(string|\Stringable $actualType, string|\Stringable $expectedTypes, Context $context)
    {
        $this->expectedTypes = (string)$expectedTypes;
        $this->actualType = (string)$actualType;

        parent::__construct(
            self::CODE,
            sprintf(self::MESSAGE, $actualType, $expectedTypes),
            $context
        );
    }
}
