<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\ValueError;

/**
 * @template-extends BaseTransformingException<ValueError>
 */
class UnexpectedValueException extends BaseTransformingException
{
    public function __construct(
        string $actual,
        string $expected,
        Context $context,
        ?\Throwable $previous = null
    ) {
        parent::__construct(new ValueError($actual, $expected, $context), $previous);
    }
}
