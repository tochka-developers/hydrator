<?php

namespace Tochka\Hydrator\Casters;

use Tochka\Hydrator\Contracts\ExtractCasterInterface;
use Tochka\Hydrator\Contracts\HydrateCasterInterface;
use Tochka\Hydrator\DTO\CastInfo;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\Exceptions\UnexpectedValueCastException;
use Tochka\Hydrator\Exceptions\WrongExpectedTypeCastException;
use Tochka\Hydrator\Exceptions\WrongValueTypeCastException;

/**
 * @since 8.1
 */
class EnumCaster implements ExtractCasterInterface, HydrateCasterInterface
{
    public function canHydrate(CastInfo $castInfo): bool
    {
        return $this->canCast($castInfo);
    }

    public function canExtract(CastInfo $castInfo): bool
    {
        return $this->canCast($castInfo);
    }

    private function canCast(CastInfo $castInfo): bool
    {
        return function_exists('enum_exists') && enum_exists($castInfo->getTypeDefinition()->getClassName());
    }

    public function extract(CastInfo $castInfo, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value) && !is_int($value)) {
            throw new WrongValueTypeCastException(gettype($value), 'string|int');
        }

        /** @var class-string<\BackedEnum>|null $expectedType */
        $expectedType = $castInfo->getTypeDefinition()->getClassName();
        if ($expectedType === null || !enum_exists($expectedType)) {
            throw new WrongExpectedTypeCastException($expectedType, 'enum');
        }

        try {
            return $expectedType::from($value);
        } catch (\ValueError) {
            throw new UnexpectedValueCastException($value, $expectedType::cases());
        }
    }

    public function hydrate(CastInfo $castInfo, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof \BackedEnum) {
            return $value;
        }

        return $value->value;
    }

    public function typeAfterHydrate(CastInfo $castInfo): TypeDefinition|UnionTypeDefinition
    {
        return new TypeDefinition(ScalarTypeEnum::TYPE_STRING());
    }

    public function typeBeforeExtract(CastInfo $castInfo): TypeDefinition|UnionTypeDefinition
    {
        return new TypeDefinition(ScalarTypeEnum::TYPE_STRING());
    }
}
