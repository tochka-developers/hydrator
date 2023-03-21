<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Carbon\Carbon;
use Tochka\Hydrator\Attributes\TimeZone;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\Exceptions\UnexpectedValueException;
use Tochka\TypeParser\TypeSystem\Types\NamedObjectType;

/**
 * @psalm-api
 */
final class CarbonExtractor implements ValueExtractorInterface
{
    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        /** @psalm-suppress RedundantCondition */
        if (!$to->type instanceof NamedObjectType || !is_a($to->type->className, Carbon::class, true)) {
            return $next($value, $to, $context);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException(gettype($value), 'string', $context);
        }

        try {
            /**
             * @psalm-ignore-var
             * @var TimeZone|null $attribute
             */
            $attribute = $to->attributes->type(TimeZone::class)->first();
            $tz = $attribute?->timezone;

            return Carbon::parse($value, $tz);
        } catch (\Throwable $e) {
            throw new UnexpectedValueException($value, 'datetime-string', $context, $e);
        }
    }
}
