<?php

namespace Tochka\Hydrator\Hydrators;

use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;

class ArrayHydrator implements ValueHydratorInterface
{
    public function hydrate(FromContainer $from, ToContainer $to, ?Context $context, callable $next): mixed
    {
        // TODO: Implement hydrate() method.
    }
}
