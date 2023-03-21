<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\TypeParser\TypeSystem\Types\ObjectType;

/**
 * @psalm-api
 */
final class ObjectExtractor implements ValueExtractorInterface
{
    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof ObjectType) {
            return $next($value, $to, $context);
        }

        if (!is_object($value) && !is_array($value)) {
            throw new UnexpectedTypeException(gettype($value), 'object|array', $context);
        }

        return (object)$value;
    }
}
