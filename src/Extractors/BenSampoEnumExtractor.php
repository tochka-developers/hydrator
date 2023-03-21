<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\Exceptions\UnexpectedValueException;
use Tochka\TypeParser\TypeSystem\Types\NamedObjectType;

/**
 * @psalm-api
 */
final class BenSampoEnumExtractor implements ValueExtractorInterface
{
    public function extract(mixed $value, ToContainer $to, Context $context, callable $next): mixed
    {
        /** @psalm-suppress RedundantCondition */
        if (!$to->type instanceof NamedObjectType || !is_a($to->type->className, Enum::class, true)) {
            return $next($value, $to, $context);
        }

        if (!is_string($value) && !is_int($value)) {
            throw new UnexpectedTypeException(gettype($value), 'string|int', $context);
        }

        /**
         * @var class-string<Enum<string|int>> $expectedType
         */
        $expectedType = $to->type->className;

        try {
            /** @psalm-suppress InvalidArgument */
            return $expectedType::fromValue($value);
        } catch (InvalidEnumMemberException $e) {
            throw new UnexpectedValueException($value, implode(',', $expectedType::getValues()), $context, $e);
        }
    }
}
