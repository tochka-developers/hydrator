<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ExtractContainer;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\ValueDefinition;

class ArrayValueExtractor implements ValueExtractorInterface
{
    public function extract(ExtractContainer $extractContainer, callable $next): mixed
    {
        if ($extractContainer->getType()->getScalarType()->isNot(ScalarTypeEnum::TYPE_ARRAY())) {
            return $next($extractContainer);
        }

        $arrayValue = $extractContainer->getValueToExtract();

        if (!is_array($arrayValue)) {
            // TODO: exception
            throw new \RuntimeException();
        }

        $valueType = $extractContainer->getType()->getValueType();

        if ($valueType === null) {
            return $arrayValue;
        }

        return array_map(function (mixed $value) use ($extractContainer, $valueType): mixed {
            return $extractContainer->getExtractor()->extractValue($value, new ValueDefinition($valueType));
        }, $arrayValue);
    }
}
