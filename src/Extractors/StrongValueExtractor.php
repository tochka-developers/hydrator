<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ExtractContainer;
use Tochka\Hydrator\DTO\ScalarTypeEnum;

class StrongValueExtractor implements ValueExtractorInterface
{
    public function extract(ExtractContainer $extractContainer, callable $next): mixed
    {
        $expectedType = $extractContainer->getType()->getScalarType();

        if ($expectedType->notIn(
            ScalarTypeEnum::TYPE_INTEGER(),
            ScalarTypeEnum::TYPE_BOOLEAN(),
            ScalarTypeEnum::TYPE_FLOAT()
        )) {
            return $next($extractContainer);
        }

        $actualType = ScalarTypeEnum::fromVarType($extractContainer->getValueToExtract());

        if ($actualType->isNot($expectedType)) {
            // TODO: exception
            throw new \RuntimeException();
        }

        return $extractContainer->getValueToExtract();
    }
}
