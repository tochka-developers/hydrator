<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\Value;

class MixedExtractor implements ValueExtractorInterface
{
    public function extract(Value $value, callable $next): mixed
    {
        if ($value->getType()->getScalarType() === ScalarTypeEnum::TYPE_MIXED) {
            return $value->getValue();
        }

        return $next($value);
    }
}
