<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Carbon\Carbon;
use Tochka\Hydrator\Attributes\TimeZone;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\Exceptions\UnexpectedValueException;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;
use Tochka\Hydrator\TypeSystem\Types\StringType;

final class CarbonExtractor implements ValueExtractorInterface
{
    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof NamedObjectType || !is_a($to->type->className, Carbon::class, true)) {
            return $next($from, $to, $context);
        }

        if (!$from->type instanceof StringType) {
            throw new UnexpectedTypeException($to->type, $from->type, $context);
        }

        try {
            /**
             * @psalm-ignore-var
             * @var TimeZone|null $attribute
             */
            $attribute = $to->attributes->type(TimeZone::class)->first();
            $tz = $attribute?->timezone;

            return Carbon::parse($from->value, $tz);
        } catch (\Throwable $e) {
            throw new UnexpectedValueException($from->value, 'datetime-string', $context, $e);
        }
    }
}
