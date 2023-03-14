<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\TypeSystem\Types\FloatType;
use Tochka\Hydrator\TypeSystem\Types\IntType;
use Tochka\Hydrator\TypeSystem\Types\StringType;

final class StringExtractor implements ValueExtractorInterface
{
    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof StringType) {
            return $next($from, $to, $context);
        }

        if (!$from->type instanceof FloatType && !$from->type instanceof IntType && !$from->type instanceof StringType) {
            throw new UnexpectedTypeException($to->type, $from->type, $context);
        }

        return (string)$from->value;
    }
}
