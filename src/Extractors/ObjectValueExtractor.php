<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ExtractContainer;
use Tochka\Hydrator\DTO\ScalarTypeEnum;

class ObjectValueExtractor implements ValueExtractorInterface
{
    public function extract(ExtractContainer $extractContainer, callable $next): mixed
    {
        if ($extractContainer->getType()->getScalarType()->isNot(ScalarTypeEnum::TYPE_OBJECT())) {
            return $next($extractContainer);
        }

        $value = $extractContainer->getValueToExtract();

        if (!is_object($value)) {
            // TODO: exception
            throw new \RuntimeException();
        }

        $className = $extractContainer->getType()->getClassName();

        if ($className === null) {
            return $value;
        }

        return $extractContainer->getExtractor()->extractObject($value, $className);
    }
}
