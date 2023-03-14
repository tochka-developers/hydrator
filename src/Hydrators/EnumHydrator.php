<?php

namespace Tochka\Hydrator\Hydrators;

use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;

class EnumHydrator implements ValueHydratorInterface
{

    public function hydrate(FromContainer $from, ToContainer $to, ?Context $context, callable $next): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof \BackedEnum) {
            return $value;
        }

        return $value->value;
    }
}
