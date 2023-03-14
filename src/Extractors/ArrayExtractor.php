<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ExtractorInterface;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\Definitions\DTO\Collection;
use Tochka\Hydrator\DTO\ArrayContext;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\BaseTransformingException;
use Tochka\Hydrator\Exceptions\SameTransformingFieldException;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\TypeSystem\Types\ArrayType;
use Tochka\Hydrator\TypeSystem\Types\MixedType;

final class ArrayExtractor implements ValueExtractorInterface
{

    public function __construct(private readonly ExtractorInterface $extractor)
    {
    }

    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof ArrayType) {
            return $next($from, $to, $context);
        }

        if (!$from->type instanceof ArrayType) {
            throw new UnexpectedTypeException($to->type, $from->type, $context);
        }

        if ($to->type->valueType instanceof MixedType) {
            return $from->value;
        }

        $result = [];
        $errors = [];

        foreach ($from->value as $key => $value) {
            try {
                $result[] = $this->extractor->extract(
                    $value,
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
