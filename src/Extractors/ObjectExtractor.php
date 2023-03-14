<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\TypeSystem\Types\ArrayType;
use Tochka\Hydrator\TypeSystem\Types\ObjectType;

final class ObjectExtractor implements ValueExtractorInterface
{
    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof ObjectType) {
            return $next($from, $to, $context);
        }

        if (!$from->type instanceof ObjectType && !$from->type instanceof ArrayType) {
            throw new UnexpectedTypeException($to->type, $from->type, $context);
        }

        return (object)$from->value;
    }
}
