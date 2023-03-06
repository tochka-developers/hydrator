<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\Value;
use Tochka\Hydrator\DTO\ValueDefinition;
use Tochka\Hydrator\Exceptions\UnexpectedValueTypeException;

class ArrayExtractor implements ValueExtractorInterface
{
    private ExtractorInterface $extractor;

    public function __construct(ExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
    }

    public function extract(Value $value, callable $next): mixed
    {
        if ($value->getType()->getScalarType() !== ScalarTypeEnum::TYPE_ARRAY) {
            return $next($value);
        }

        $arrayValue = $value->getValue();

        if (!is_array($arrayValue)) {
            throw new UnexpectedValueTypeException();
        }

        $valueType = $value->getType()->getValueType();

        if ($valueType === null) {
            return $arrayValue;
        }

        $valueDefinition = new ValueDefinition($valueType);

        return array_map(function (mixed $arrayValue) use ($valueDefinition): mixed {
            return $this->extractor->extractValue($arrayValue, $valueDefinition);
        }, $arrayValue);
    }
}
