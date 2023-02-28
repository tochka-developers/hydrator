<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ExtractContainer;
use Tochka\Hydrator\DTO\ScalarTypeEnum;

class StringValueExtractor implements ValueExtractorInterface
{
    public function extract(ExtractContainer $extractContainer, callable $next): mixed
    {
        $expectedType = $extractContainer->getType()->getScalarType();

        if ($expectedType->isNot(ScalarTypeEnum::TYPE_STRING())) {
            return $next($extractContainer);
        }

        $actualType = ScalarTypeEnum::fromVarType($extractContainer->getValueToExtract());

        if ($actualType->notIn(
            ScalarTypeEnum::TYPE_FLOAT(),
            ScalarTypeEnum::TYPE_INTEGER(),
            ScalarTypeEnum::TYPE_STRING()
        )) {
            // TODO: exception
            throw new \RuntimeException();
        }

        return (string)$extractContainer->getValueToExtract();
    }
}
