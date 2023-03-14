<?php

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\MakeTargetError;

/**
 * @template-extends BaseTransformingException<MakeTargetError>
 */
class MakeTargetException extends BaseTransformingException
{
    public function __construct(string $className, Context $context, ?\Throwable $previous = null)
    {
        parent::__construct(new MakeTargetError($className, $context), $previous);
    }
}
