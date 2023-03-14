<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;

interface ValueExtractorInterface
{
    /**
     * @template TValueType
     * @template TReturnType
     * @param FromContainer<TValueType> $from
     * @param ToContainer<TReturnType> $to
     * @param callable(FromContainer, ToContainer, Context): mixed $next
     * @return TReturnType
     */
    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed;
}
