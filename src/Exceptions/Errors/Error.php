<?php

namespace Tochka\Hydrator\Exceptions\Errors;

use Illuminate\Contracts\Support\Arrayable;

abstract class Error implements Arrayable
{
    public function __construct(string $code, string $message)
    {

    }

    public function toArray(): array
    {

    }
}
