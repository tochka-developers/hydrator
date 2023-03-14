<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\TypeSystem\TypeComparator;
use Tochka\Hydrator\TypeSystem\Types\BoolType;
use Tochka\Hydrator\TypeSystem\Types\FloatType;
use Tochka\Hydrator\TypeSystem\Types\IntType;

final class StrongScalarExtractor implements ValueExtractorInterface
{
    public function __construct(
        private readonly TypeComparator $typeComparator
    ) {
    }

    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof IntType && !$to->type instanceof BoolType && !$to->type instanceof FloatType) {
            return $next($from, $to, $context);
        }

        if (!$this->typeComparator->compare($to->type, $from->type)) {
            throw new UnexpectedTypeException($to->type, $from->type, $context);
        }

        return $from->value;
    }
}
