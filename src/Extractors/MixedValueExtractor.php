<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ExtractContainer;
use Tochka\Hydrator\DTO\ScalarTypeEnum;

class MixedValueExtractor implements ValueExtractorInterface
{
    public function extract(ExtractContainer $extractContainer, callable $next): mixed
    {
        if ($extractContainer->getType()->getScalarType()->is(ScalarTypeEnum::TYPE_MIXED())) {
            return $extractContainer->getValueToExtract();
        }

        return $next($extractContainer);
    }
}
