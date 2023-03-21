<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\TypeParser\TypeSystem\Types\StringType;

/**
 * @psalm-api
 */
final class StringExtractor implements ValueExtractorInterface
{
    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof StringType) {
            return $next($value, $to, $context);
        }

        if (!is_float($value) && !is_int($value) && !is_string($value)) {
            throw new UnexpectedTypeException(gettype($value), 'string|int|float', $context);
        }

        return (string)$value;
    }
}
