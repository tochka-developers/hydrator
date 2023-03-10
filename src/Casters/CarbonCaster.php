<?php

namespace Tochka\Hydrator\Casters;

use Carbon\Carbon;
use Tochka\Hydrator\Annotations\DateTimeFormat;
use Tochka\Hydrator\Annotations\TimeZone;
use Tochka\Hydrator\Contracts\ExtractCasterInterface;
use Tochka\Hydrator\Contracts\HydrateCasterInterface;
use Tochka\Hydrator\DTO\CastInfo\CastInfoInterface;
use Tochka\Hydrator\DTO\ScalarTypeEnum;
use Tochka\Hydrator\DTO\TypeDefinition;
use Tochka\Hydrator\DTO\UnionTypeDefinition;
use Tochka\Hydrator\Exceptions\WrongValueTypeCastException;

class CarbonCaster implements ExtractCasterInterface, HydrateCasterInterface
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
        return class_exists('\\Carbon\\Carbon') && is_a($castInfo->getClassName(), Carbon::class, true);
    }

    public function hydrate(CastInfoInterface $castInfo, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Carbon) {
            return $value;
        }

        /**
         * @psalm-ignore-var
         * @var DateTimeFormat|null $attribute
         */
        $attribute = $castInfo->getAttributes()->type(DateTimeFormat::class)->first();
        $format = $attribute?->format ?? Carbon::DEFAULT_TO_STRING_FORMAT;

        return $value->format($format);
    }

    public function extract(CastInfoInterface $castInfo, mixed $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value) && !$value instanceof \DateTimeInterface) {
            throw new WrongValueTypeCastException('mixed', 'datetime-string');
        }

        try {
            /**
             * @psalm-ignore-var
             * @var TimeZone|null $attribute
             */
            $attribute = $castInfo->getAttributes()->type(TimeZone::class)->first();
            $tz = $attribute?->timezone;

            return Carbon::parse($value, $tz);
        } catch (\Throwable) {
            throw new WrongValueTypeCastException('mixed', 'datetime-string');
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
