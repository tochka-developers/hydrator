<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\Value;

interface ValueExtractorInterface
{
    /**
     * @param Value $value
     * @param callable(Value): mixed $next
     * @return mixed
     */
    public function extract(Value $value, callable $next): mixed;
}
