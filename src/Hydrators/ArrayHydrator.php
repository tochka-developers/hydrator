<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Hydrators;

use Tochka\Hydrator\Contracts\HydratorInterface;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\ArrayContext;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\Exceptions\BaseTransformingException;
use Tochka\Hydrator\Exceptions\SameTransformingFieldException;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 */
class ArrayHydrator implements ValueHydratorInterface
{
    public function __construct(
        private readonly HydratorInterface $hydrator
    ) {
    }

    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed
    {
        if (is_iterable($value)) {
            return $this->hydrateArray($value, $context);
        }

        return $next($value, $attributes, $context);
    }

    private function hydrateArray(iterable $value, Context $context): array
    {
        $result = [];
        $errors = [];

        foreach ($value as $key => $item) {
            try {
                $result[] = $this->hydrator->hydrate($item, context: new ArrayContext($key, previous: $context));
            } catch (BaseTransformingException $e) {
                $errors[] = $e;
            }
        }

        if (!empty($errors)) {
            throw new SameTransformingFieldException($errors, $context);
        }

        return $result;
    }
}
