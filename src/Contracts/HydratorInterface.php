<?php

namespace Tochka\Hydrator\Contracts;

use Illuminate\Contracts\Container\BindingResolutionException;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\TypeSystem\TypeInterface;

interface HydratorInterface
{
    /**
     * @param ValueHydratorInterface|class-string<ValueHydratorInterface> $extractor
     * @return void
     * @throws BindingResolutionException
     */
    public function registerHydrator(ValueHydratorInterface|string $hydrator): void;

    public function hydrate(mixed $value, TypeInterface $type, Collection $attributes, ?Context $context = null): mixed;
}
