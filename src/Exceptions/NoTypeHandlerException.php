<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\NoTypeHandlerError;

/**
 * @template-extends BaseTransformingException<NoTypeHandlerError>
 */
class NoTypeHandlerException extends BaseTransformingException
{
    public function __construct(Context $context, ?\Throwable $previous = null)
    {
        parent::__construct(new NoTypeHandlerError($context), $previous);
    }
}
