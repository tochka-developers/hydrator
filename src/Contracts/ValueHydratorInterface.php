<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\HydrateContainer;

interface ValueHydratorInterface
{
    /**
     * @param HydrateContainer $extractContainer
     * @param callable(HydrateContainer): mixed $next
     * @return mixed
     */
    public function hydrate(HydrateContainer $extractContainer, callable $next): mixed;
}
