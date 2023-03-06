<?php

declare(strict_types=1);

namespace Tochka\Hydrator\ExtendedReflection\Traits;

use Tochka\Hydrator\ExtendedReflection\Enums\ClassModifierEnum;
use Tochka\Hydrator\ExtendedReflection\Enums\ModifierEnumInterface;

trait ModifiersTrait
{
    private function checkModifiers(
        int $actualModifiers,
        ModifierEnumInterface $expectModifier,
        ModifierEnumInterface ...$expectModifiers
    ): bool {
        return (bool)($actualModifiers
            & array_reduce(
                $expectModifiers,
                fn (int $carry, ClassModifierEnum $modifier): int => $carry | $modifier->getReflectionConst(),
                $expectModifier->getReflectionConst()
            )
        );
    }
}
