<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Hydrators;

use Illuminate\Contracts\Support\Arrayable;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 */
class ArrayableHydrator implements ValueHydratorInterface
{
    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed
    {
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        return $next($value, $attributes, $context);
    }
}
