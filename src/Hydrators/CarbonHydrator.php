<?php

namespace Tochka\Hydrator\Hydrators;

use Carbon\Carbon;
use Tochka\Hydrator\Attributes\DateTimeFormat;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;

class CarbonHydrator implements ValueHydratorInterface
{

    public function hydrate(FromContainer $from, ToContainer $to, ?Context $context, callable $next): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Carbon) {
            return $value;
        }

        /**
         * @psalm-ignore-var
         * @var DateTimeFormat|null $attribute
         */
        $attribute = $castInfo->getAttributes()->type(DateTimeFormat::class)->first();
        $format = $attribute?->format ?? Carbon::DEFAULT_TO_STRING_FORMAT;

        return $value->format($format);
    }
}
