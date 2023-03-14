<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\TypeError;

/**
 * @template-extends BaseTransformingException<TypeError>
 */
class UnexpectedTypeException extends BaseTransformingException
{
    public function __construct(
        string|\Stringable $actualType,
        string|\Stringable $expectedTypes,
        Context $context,
        ?\Throwable $previous = null
    ) {
        parent::__construct(new TypeError($actualType, $expectedTypes, $context), $previous);
    }
}
