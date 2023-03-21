<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\TypeParser\TypeSystem\Types\NullType;

/**
 * @psalm-api
 */
final class NullExtractor implements ValueExtractorInterface
{
    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof NullType) {
            return $next($value, $to, $context);
        }

        if ($value !== null) {
            throw new UnexpectedTypeException(gettype($value), 'null', $context);
        }

        return null;
    }
}
