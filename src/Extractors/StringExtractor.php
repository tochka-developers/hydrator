<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\Value;

class StringExtractor implements ValueExtractorInterface
{
    public function extract(Value $value, callable $next): mixed
    {
        $expectedType = $value->getType()->getScalarType();

        if ($expectedType !== ScalarTypeEnum::TYPE_STRING) {
            return $next($value);
        }

        $actualType = ScalarTypeEnum::fromVarType($value->getValue());

        if ($actualType->notOneOf(
            ScalarTypeEnum::TYPE_FLOAT,
            ScalarTypeEnum::TYPE_INTEGER,
            ScalarTypeEnum::TYPE_STRING
        )) {
            // TODO: exception
            throw new \RuntimeException();
        }

        return (string)$value->getValue();
    }
}
