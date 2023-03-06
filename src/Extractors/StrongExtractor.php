<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\Value;
use Tochka\Hydrator\Exceptions\UnexpectedValueTypeException;

class StrongExtractor implements ValueExtractorInterface
{
    public function extract(Value $value, callable $next): mixed
    {
        $expectedType = $value->getType()->getScalarType();

        if ($expectedType->notOneOf(
            ScalarTypeEnum::TYPE_INTEGER,
            ScalarTypeEnum::TYPE_BOOLEAN,
            ScalarTypeEnum::TYPE_FLOAT
        )) {
            return $next($value);
        }

        $actualType = ScalarTypeEnum::fromVarType($value->getValue());

        if ($actualType !== $expectedType) {
            throw new UnexpectedValueTypeException();
        }

        return $value->getValue();
    }
}
