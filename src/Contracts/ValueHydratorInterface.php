<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\Context;
use Tochka\TypeParser\Collection;

/**
 * @psalm-type AfterHydrateType = scalar|array|object|null
 */
interface ValueHydratorInterface
{
    /**
     * @template TValueType
     * @param TValueType $value
     * @param callable(TValueType, Collection, Context): AfterHydrateType $next
     * @return AfterHydrateType
     */
    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed;
}
