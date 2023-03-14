<?php

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\ContainerError;

/**
 * @template-extends BaseTransformingException<ContainerError>
 */
class ContainerValueException extends BaseTransformingException
{
    public function __construct(string $message, Context $context, ?\Throwable $previous = null)
    {
        parent::__construct(new ContainerError($message, $context), $previous);
    }
}
