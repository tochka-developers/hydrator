<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Hydrators;

use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 */
class EnumHydrator implements ValueHydratorInterface
{
    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return $next($value, $attributes, $context);
    }
}
