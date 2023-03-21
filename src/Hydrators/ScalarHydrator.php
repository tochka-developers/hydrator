<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Hydrators;

use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 */
class ScalarHydrator implements ValueHydratorInterface
{
    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed
    {
        if (
            is_string($value)
            || is_int($value)
            || is_float($value)
            || is_bool($value)
            || (is_object($value) && get_class($value) === \stdClass::class)
            || $value === null
        ) {
            return $value;
        }

        return $next($value, $attributes, $context);
    }
}
