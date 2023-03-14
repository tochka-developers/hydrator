<?php

declare(strict_types=1);

namespace Tochka\Hydrator\Extractors;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Tochka\Hydrator\Contracts\ValueExtractorInterface;
use Tochka\Hydrator\DTO\Context;
use Tochka\Hydrator\DTO\FromContainer;
use Tochka\Hydrator\DTO\ToContainer;
use Tochka\Hydrator\Exceptions\UnexpectedTypeException;
use Tochka\Hydrator\Exceptions\UnexpectedValueException;
use Tochka\Hydrator\TypeSystem\Types\IntType;
use Tochka\Hydrator\TypeSystem\Types\NamedObjectType;
use Tochka\Hydrator\TypeSystem\Types\StringType;

final class BenSampoEnumExtractor implements ValueExtractorInterface
{
    public function extract(FromContainer $from, ToContainer $to, Context $context, callable $next): mixed
    {
        if (!$to->type instanceof NamedObjectType || !is_a($to->type->className, Enum::class, true)) {
            return $next($from, $to, $context);
        }

        if (!$from->type instanceof StringType && !$from->type instanceof IntType) {
            throw new UnexpectedTypeException($to->type, $from->type, $context);
        }

        try {
            /**
             * @psalm-ignore-var
             * @var class-string<Enum> $expectedType
             */
            $expectedType = $to->type->className;
            return $expectedType::fromValue($from->value);
        } catch (InvalidEnumMemberException $e) {
            throw new UnexpectedValueException($from->value, implode(',', $expectedType::getValues()), $context, $e);
        }
    }
}
