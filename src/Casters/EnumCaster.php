<?php

namespace Tochka\Hydrator\Casters;

use Tochka\Hydrator\Contracts\ExtractCasterInterface;
use Tochka\Hydrator\Contracts\HydrateCasterInterface;
use Tochka\Hydrator\DTO\CastInfo\CastInfoInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;
use Tochka\Hydrator\Exceptions\UnexpectedValueCastException;
use Tochka\Hydrator\Exceptions\WrongExpectedTypeCastException;
use Tochka\Hydrator\Exceptions\WrongValueTypeCastException;

/**
 * @since 8.1
 */
class EnumCaster implements ExtractCasterInterface, HydrateCasterInterface
{
    public function canHydrate(CastInfoInterface $castInfo): bool
    {
        return $this->canCast($castInfo);
    }

    public function canExtract(CastInfoInterface $castInfo): bool
    {
        return $this->canCast($castInfo);
    }

    private function canCast(CastInfoInterface $castInfo): bool
    {
        return function_exists('enum_exists') && enum_exists($castInfo->getClassName());
    }

    public function extract(CastInfoInterface $castInfo, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value) && !is_int($value)) {
            throw new WrongValueTypeCastException(gettype($value), 'string|int');
        }

        /** @var class-string<\BackedEnum>|null $expectedType */
        $expectedType = $castInfo->getClassName();
        if ($expectedType === null || !enum_exists($expectedType)) {
            throw new WrongExpectedTypeCastException($expectedType, 'enum');
        }

        try {
            return $expectedType::from($value);
        } catch (\ValueError) {
            throw new UnexpectedValueCastException($value, $expectedType::cases());
        }
    }

    public function hydrate(CastInfoInterface $castInfo, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof \BackedEnum) {
            return $value;
        }

        return $value->value;
    }

    public function typeAfterHydrate(CastInfoInterface $castInfo): TypeDefinition|UnionTypeDefinition
    {
        return new TypeDefinition(ScalarTypeEnum::TYPE_STRING);
    }

    public function typeBeforeExtract(CastInfoInterface $castInfo): TypeDefinition|UnionTypeDefinition
    {
        return new TypeDefinition(ScalarTypeEnum::TYPE_STRING);
    }
}
