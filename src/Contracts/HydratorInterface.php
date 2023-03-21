<?php

namespace Tochka\Hydrator\Contracts;

use Illuminate\Contracts\Container\BindingResolutionException;
use Tochka\Hydrator\DTO\Context;
use Tochka\TypeParser\Collection;
use Tochka\TypeParser\TypeSystem\TypeInterface;

/**
 * @psalm-api
 *
 * @psalm-import-type AfterHydrateType from ValueHydratorInterface
 */
interface HydratorInterface
{
    /**
     * @template T of ValueHydratorInterface
     * @param ValueHydratorInterface|class-string<T> $hydrator
     * @return void
     */
    public function registerHydrator(ValueHydratorInterface|string $hydrator): void;

    /**
     * @return AfterHydrateType
     */
    public function hydrate(mixed $value, ?Collection $attributes = null, ?Context $context = null): mixed;
}
