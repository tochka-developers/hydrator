<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\Errors;

/**
 * @template-extends BaseTransformingException<Errors>
 */
class SameTransformingFieldException extends BaseTransformingException
{
    /**
     * @param list<BaseTransformingException> $exceptions
     */
    public function __construct(array $exceptions, Context $context, ?\Throwable $previous = null)
    {
        $errors = [];

        foreach ($exceptions as $exception) {
            /** @psalm-suppress DocblockTypeContradiction */
            if ($exception instanceof SameTransformingFieldException) {
                $errors[] = $exception->getError()->errors;
            } else {
                $errors[] = [$exception->getError()];
            }
        }

        parent::__construct(new Errors(array_merge(...$errors), $context), $previous);
    }
}
