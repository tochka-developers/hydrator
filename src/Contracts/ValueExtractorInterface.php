<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;

/**
 * @psalm-type BeforeHydrateType = scalar|array|object|null
 */
interface ValueExtractorInterface
{
    /**
     * @template TReturnType
     * @param BeforeHydrateType $value
     * @param ToContainer<TReturnType> $to
     * @param callable(BeforeHydrateType, ToContainer<TReturnType>, Context): TReturnType $next
     * @return TReturnType
     */
    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed;
}
