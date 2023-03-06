<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\DTO\StringRestrictionEnum;
use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @implements TypeInterface<string>
 */
final class StringType implements TypeInterface
{
    public readonly StringRestrictionEnum $restriction;

    public function __construct(StringRestrictionEnum $restriction = StringRestrictionEnum::NONE)
    {
        $this->restriction = $restriction;
    }
}
