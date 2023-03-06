<?php

namespace Tochka\Hydrator\Hydrators;

use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\HydrateContainer;

class MixedHydrator implements ValueHydratorInterface
{
    public function hydrate(HydrateContainer $extractContainer, callable $next): mixed
    {
        // TODO: Implement hydrate() method.
    }
}
