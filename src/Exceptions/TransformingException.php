<?php

namespace Tochka\Hydrator\Exceptions;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\Errors\Error;

abstract class TransformingException extends BaseHydratorException
{
    private Context $context;
    private Error $error;

    public function __construct(Error $error, Context $context, ?\Throwable $previous)
    {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
        $this->error = $error;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getError(): Error
    {
        return $this->error;
    }
}
