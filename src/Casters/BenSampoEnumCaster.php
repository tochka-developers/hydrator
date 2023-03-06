<?php

namespace Tochka\Hydrator\Casters;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Tochka\Hydrator\Contracts\ExtractCasterInterface;
use Tochka\Hydrator\Contracts\HydrateCasterInterface;
use Tochka\Hydrator\DTO\CastInfo\CastInfoInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;
use Tochka\Hydrator\Exceptions\UnexpectedValueCastException;
use Tochka\Hydrator\Exceptions\WrongExpectedTypeCastException;

class BenSampoEnumCaster implements ExtractCasterInterface, HydrateCasterInterface
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
        return class_exists('\\BenSampo\\Enum\\Enum') && is_a($castInfo->getClassName(), Enum::class, true);
    }

    public function hydrate(CastInfoInterface $castInfo, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Enum) {
            return $value;
        }

        return $value->value;
    }

    public function extract(CastInfoInterface $castInfo, mixed $value): ?Enum
    {
        if ($value === null) {
            return null;
        }

        /** @var class-string<Enum>|null $expectedType */
        $expectedType = $castInfo->getClassName();
        if ($expectedType === null || !is_a($expectedType, Enum::class, true)) {
            throw new WrongExpectedTypeCastException($expectedType, Enum::class);
        }

        try {
            return $expectedType::fromValue($value);
        } catch (InvalidEnumMemberException) {
            throw new UnexpectedValueCastException($value, $expectedType::getValues());
        }
    }

    public function typeBeforeExtract(CastInfoInterface $castInfo): TypeDefinition|UnionTypeDefinition
    {
        return new TypeDefinition(ScalarTypeEnum::TYPE_STRING);
    }

    public function typeAfterHydrate(CastInfoInterface $castInfo): TypeDefinition|UnionTypeDefinition
    {
        return new TypeDefinition(ScalarTypeEnum::TYPE_STRING);
    }
}
