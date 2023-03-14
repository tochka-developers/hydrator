<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\Exceptions\Enums\ExceptionCodeEnum;
use Tochka\Hydrator\Exceptions\Errors\Error;

/**
 * @template TError of Error
 */
abstract class BaseTransformingException extends BaseHydrateException
{
    /**
     * @var TError
     */
    private Error $error;

    /**
     * @param TError $error
     */
    public function __construct(Error $error, ?\Throwable $previous)
    {
        parent::__construct($error->message, ExceptionCodeEnum::TRANSFORMING, $previous);

        $this->error = $error;
    }

    /**
     * @return TError
     */
    public function getError(): Error
    {
        return $this->error;
    }
}
