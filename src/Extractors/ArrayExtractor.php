<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\ArrayContext;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\BaseTransformingException;
use Tochka\Hydrator\Exceptions\SameTransformingFieldException;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\TypeParser\Collection;
use Tochka\TypeParser\TypeSystem\Types\ArrayType;
use Tochka\TypeParser\TypeSystem\Types\MixedType;

/**
 * @psalm-api
 */
final class ArrayExtractor implements ValueExtractorInterface
{
    public function __construct(
        private readonly ExtractorInterface $extractor
    ) {
    }

    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof ArrayType) {
            return $next($value, $to, $context);
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException(gettype($value), 'array', $context);
        }

        if ($to->type->valueType instanceof MixedType) {
            return $value;
        }

        $result = [];
        $errors = [];

        foreach ($value as $key => $item) {
            try {
                $result[] = $this->extractor->extract(
                    $item,
                    $to->type->valueType,
                    new Collection(),
                    new ArrayContext($key, previous: $context)
                );
            } catch (BaseTransformingException $e) {
                $errors[] = $e;
            }
        }

        if (!empty($errors)) {
            throw new SameTransformingFieldException($errors, $context);
        }

        return $result;
    }
}
