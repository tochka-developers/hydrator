<?php

namespace Tochka\Hydrator\Contracts;

use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;

interface ValueHydratorInterface
{
    /**
     * @param callable(FromContainer, ToContainer, ?Context): mixed $next
     */
    public function hydrate(FromContainer $from, ToContainer $to, ?Context $context, callable $next): mixed;
}
