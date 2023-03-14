<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\DTO\BoolRestrictionEnum;
use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @implements TypeInterface<bool>
 */
final class BoolType implements TypeInterface
{
    public readonly BoolRestrictionEnum $restriction;

    public function __construct(BoolRestrictionEnum $restriction = BoolRestrictionEnum::NONE)
    {
        $this->restriction = $restriction;
    }

    public function __toString(): string
    {
        return 'bool';
    }
}
