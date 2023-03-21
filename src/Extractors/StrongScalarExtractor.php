<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\TypeParser\TypeSystem\TypeInterface;
use Tochka\TypeParser\TypeSystem\Types\BoolType;
use Tochka\TypeParser\TypeSystem\Types\FloatType;
use Tochka\TypeParser\TypeSystem\Types\IntType;

/**
 * @psalm-api
 */
final class StrongScalarExtractor implements ValueExtractorInterface
{
    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        if ($to->type instanceof IntType) {
            return $this->returnIfTrue(is_int($value), $value, $to->type, $context);
        }
        if ($to->type instanceof FloatType) {
            return $this->returnIfTrue(is_float($value), $value, $to->type, $context);
        }
        if ($to->type instanceof BoolType) {
            return $this->returnIfTrue(is_bool($value), $value, $to->type, $context);
        }

        return $next($value, $to, $context);
    }

    private function returnIfTrue(bool $return, mixed $value, TypeInterface $type, Context $context): mixed
    {
        if ($return) {
            return $value;
        } else {
            throw new UnexpectedTypeException(gettype($value), $type, $context);
        }
    }
}
