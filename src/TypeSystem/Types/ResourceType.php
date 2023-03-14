<?php

declare(strict_types=1);

namespace Tochka\Hydrator\TypeSystem\Types;

use Tochka\Hydrator\TypeSystem\TypeInterface;

/**
 * @psalm-api
 * @psalm-immutable
 * @implements TypeInterface<resource>
 */
final class ResourceType implements TypeInterface
{
    public function __toString(): string
    {
        return 'resource';
    }
}
