<?php

namespace Tochka\Hydrator\Casters;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Tochka\Hydrator\Contracts\ExtractCasterInterface;
use Tochka\Hydrator\Contracts\HydrateCasterInterface;
use Tochka\Hydrator\DTO\CastInfo;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\Exceptions\UnexpectedValueCastException;
use Tochka\Hydrator\Exceptions\WrongExpectedTypeCastException;

class BenSampoEnumCaster implements ExtractCasterInterface, HydrateCasterInterface
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
        return class_exists('\\BenSampo\\Enum\\Enum') && is_a($castInfo->getTypeDefinition()->getClassName(), Enum::class, true);
    }

    public function hydrate(CastInfo $castInfo, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Enum) {
            return $value;
        }

        return $value->value;
    }

    public function extract(CastInfo $castInfo, mixed $value): ?Enum
    {
        if ($value === null) {
            return null;
        }

        /** @var class-string<Enum>|null $expectedType */
        $expectedType = $castInfo->getTypeDefinition()->getClassName();
        if ($expectedType === null || !is_a($expectedType, Enum::class, true)) {
            throw new WrongExpectedTypeCastException($expectedType, Enum::class);
        }

        try {
            return $expectedType::fromValue($value);
        } catch (InvalidEnumMemberException) {
            throw new UnexpectedValueCastException($value, $expectedType::getValues());
        }
    }

    public function typeBeforeExtract(CastInfo $castInfo): TypeDefinition|UnionTypeDefinition
    {
        return new TypeDefinition(ScalarTypeEnum::TYPE_STRING());
    }

    public function typeAfterHydrate(CastInfo $castInfo): TypeDefinition|UnionTypeDefinition
    {
        return new TypeDefinition(ScalarTypeEnum::TYPE_STRING());
    }
}
