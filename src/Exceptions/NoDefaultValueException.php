<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\NoDefaultError;

/**
 * @template-extends BaseTransformingException<NoDefaultError>
 */
class NoDefaultValueException extends BaseTransformingException
{
    public function __construct(Context $context, ?\Throwable $previous = null)
    {
        parent::__construct(new NoDefaultError($context), $previous);
    }
}
