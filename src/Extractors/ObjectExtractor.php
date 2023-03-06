<?php

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\Value;
use Tochka\Hydrator\Exceptions\UnexpectedValueTypeException;

class ObjectExtractor implements ValueExtractorInterface
{
    private ExtractorInterface $extractor;

    public function __construct(ExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
    }

    public function extract(Value $value, callable $next): mixed
    {
        if ($value->getType()->getScalarType() !== ScalarTypeEnum::TYPE_OBJECT) {
            return $next($value);
        }

        $objectValue = $value->getValue();

        if (!is_object($objectValue)) {
            throw new UnexpectedValueTypeException();
        }

        $className = $value->getType()->getClassName();

        if ($className === null) {
            return $objectValue;
        }

        return $this->extractor->extractObject($objectValue, $className);
    }
}
