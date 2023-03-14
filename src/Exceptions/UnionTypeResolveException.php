<?php

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\UnionTypeError;

/**
 * @template-extends BaseTransformingException<UnionTypeError>
 */
class UnionTypeResolveException extends BaseTransformingException
{
    /**
     * @param list<BaseTransformingException> $exceptions
     */
    public function __construct(array $exceptions, Context $context, ?\Throwable $previous = null)
    {
        $errors = [];

        foreach ($exceptions as $exception) {
            $errors[] = [$exception->getError()];
        }

        parent::__construct(new UnionTypeError(array_merge(...$errors), $context), $previous);
    }
}
