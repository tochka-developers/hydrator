<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Hydrators;

use Tochka\Hydrator\Attributes\DateTimeFormat;
use Tochka\Hydrator\Contracts\ValueHydratorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\TypeParser\Collection;

/**
 * @psalm-api
 */
class DateTimeHydrator implements ValueHydratorInterface
{
    public function hydrate(mixed $value, Collection $attributes, Context $context, callable $next): mixed
    {
        if ($value instanceof \DateTime) {
            return $this->hydrateDateTime($value, $attributes);
        }

        return $next($value, $attributes, $context);
    }

    private function hydrateDateTime(\DateTime $value, Collection $attributes): string
    {
        /**
         * @psalm-ignore-var
         * @var DateTimeFormat|null $formatAttribute
         */
        $formatAttribute = $attributes->type(DateTimeFormat::class)->first();
        $format = $formatAttribute?->format ?? \DateTimeInterface::ATOM;

        return $value->format($format);
    }
}
