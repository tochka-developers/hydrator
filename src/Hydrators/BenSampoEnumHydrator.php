<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Hydrators;

use BenSampo\Enum\Enum;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 */
class BenSampoEnumHydrator implements ValueHydratorInterface
{
    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed
    {
        if ($value instanceof Enum) {
            return $value->value;
        }

        return $next($value, $attributes, $context);
    }
}
